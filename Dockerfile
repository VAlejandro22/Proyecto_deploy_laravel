###############################################
# Imagen para ejecutar SOLO la aplicación Laravel
# Base de datos y Redis se levantan como servicios
# separados en Railway. Este contenedor solo corre
# la app y se conecta vía variables de entorno:
#  DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#  REDIS_HOST, REDIS_PORT, REDIS_PASSWORD (si aplica)
###############################################

FROM php:8.2-fpm AS php-base

# Instalar dependencias del sistema y extensiones necesarias.
# Incluimos soporte tanto para MySQL como Postgres (opcional) y Redis.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        zip \
        bcmath \
        gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario no root para ejecutar la app
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

WORKDIR /var/www/html

COPY . .

# Instalar dependencias PHP (sin dev para producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && php artisan route:cache || true \
    && php artisan view:cache || true

# Etapa de build de assets para asegurar la persistencia del directorio public/build
FROM node:20-alpine AS assets-builder
WORKDIR /app
# Copiamos package y lock para instalar TODAS las dependencias (incluye tailwind y postcss)
COPY package.json package-lock.json* ./
RUN npm ci
# Copiar configuraciones necesarias para procesar Tailwind
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./
# Copiar recursos (CSS/JS/Views) para que tailwind pueda detectar clases
COPY resources ./resources
# Asegurar existencia de directorio public antes del build
RUN mkdir -p public && npm run build && \
    echo "[ASSETS] Manifest:" && cat public/build/manifest.json && \
    echo "[ASSETS] Primeras líneas CSS:" && head -n 40 public/build/assets/*.css || true

# Volver a la imagen base PHP y copiar sólo los assets generados
FROM php-base
COPY --from=assets-builder /app/public/build /var/www/html/public/build
RUN ls -l public/build || true && head -n 20 public/build/assets/*.css || true

# Ajustar permisos de las carpetas de Laravel
RUN chown -R www:www /var/www/html && \
    chmod -R 775 storage && \
    chmod -R 775 bootstrap/cache

USER www

# Railway asigna el puerto en $PORT
EXPOSE 8080

# Healthcheck simple (opcional)
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
  CMD php -r "exit(!@fsockopen('127.0.0.1', getenv('PORT') ?: 8080));"

# Comando de inicio: limpia y recompone cache de config con variables
# de entorno de Railway, ejecuta migraciones y levanta el servidor.
ENV PORT=8080
CMD ["sh","-c","php artisan config:clear && php artisan migrate --force --no-interaction && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=${PORT}"]
