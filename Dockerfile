# Imagen base con PHP 8.2 FPM
FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        bcmath \
        gd

# Instalar extensión de Redis
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario para Laravel
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Laravel sin ejecutar scripts (más rápido)
RUN composer install --no-dev --optimize-autoloader

# Generar cache de configuración (opcional)
RUN php artisan config:cache || true

# Ajustar permisos
RUN chown -R www:www /var/www/html && \
    chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/bootstrap/cache

# Cambiar usuario
USER www

# Railway asigna el puerto en la variable $PORT
EXPOSE 8000

# Comando de inicio para Railway
# Usa php artisan serve, exponiendo el puerto dinámico
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
