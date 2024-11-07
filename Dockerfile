# Use official PHP image as the base
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/* 

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents to the working directory
COPY . /var/www

# Install dependencies with Composer (production)
RUN composer install --no-dev --optimize-autoloader

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Ensure permissions for Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Start both Nginx and PHP-FPM
CMD service nginx start && php-fpm
