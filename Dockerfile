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
    && php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

# Construir assets frontend si existe package.json (Vite/Tailwind)
RUN if [ -f package.json ]; then \
      curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
      apt-get update && apt-get install -y nodejs && \
      npm ci && npm run build && \
      rm -rf node_modules && apt-get purge -y nodejs && apt-get autoremove -y && rm -rf /var/lib/apt/lists/* ; \
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

# Comando de inicio. No levanta DB ni Redis: solo la app.
# Migraciones se pueden correr manualmente en otro deploy o activando la línea comentada.
ENV PORT=8080
# CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT}
CMD ["sh","-c","php artisan serve --host=0.0.0.0 --port=${PORT}"]
