###############################################
# Imagen para ejecutar SOLO la aplicación Laravel
# Base de datos y Redis se levantan como servicios
# separados en Railway. Este contenedor solo corre
# la app y se conecta vía variables de entorno:
#  DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#  REDIS_HOST, REDIS_PORT, REDIS_PASSWORD (si aplica)
###############################################

FROM php:8.2-fpm

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

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias PHP (sin dev para producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && php artisan route:cache || true \
    && php artisan view:cache || true

# Construir assets frontend si existe package.json (Vite/Tailwind)
RUN if [ -f package.json ]; then \
            echo "[BUILD] Instalando Node y devDependencies para Vite/Tailwind"; \
            curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
            apt-get update && apt-get install -y nodejs && \
            npm ci && \
            npm run build && \
            echo "[BUILD] Contenido generado en public/build:" && ls -1 public/build || true && \
            echo "[BUILD] Manifest:" && cat public/build/manifest.json || true && \
            npm prune --production && \
            rm -rf node_modules && apt-get purge -y nodejs && apt-get autoremove -y && rm -rf /var/lib/apt/lists/* ; \
        else \
            echo "[BUILD] No se encontró package.json. Saltando build frontend"; \
        fi

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
