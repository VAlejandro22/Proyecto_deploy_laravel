FROM php:8.2-fpm

# Instalar solo lo esencial
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

# Instalar Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario para Laravel
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar y instalar dependencias
COPY . .
RUN composer install --no-scripts --optimize-autoloader

# Permisos
RUN chown -R www:www /var/www/html && \
    chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/bootstrap/cache

USER www
EXPOSE 9000
CMD ["php-fpm"]