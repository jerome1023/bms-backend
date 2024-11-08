# Use PHP 8.2
FROM php:8.2-fpm

# Install system dependencies and PostgreSQL PHP extensions
RUN apt-get update && apt-get install -y \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    libzip-dev \
    unzip \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo_pgsql pgsql

# Set the working directory
WORKDIR /var/www/app
COPY . .

# Set up permissions
RUN chown -R www-data:www-data /var/www/app \
    && chmod -R 775 /var/www/app/storage

# Install Composer
COPY --from=composer:2.6.5 /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Command for PHP-FPM to run in Railway
CMD ["php-fpm"]
