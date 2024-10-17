# Use the official PHP image with Apache
FROM php:8.1-apache

# Set the working directory
WORKDIR /var/www/html

# Copy the current directory contents into the container at /var/www/html
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install

# Set the environment variables for Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN echo "DocumentRoot ${APACHE_DOCUMENT_ROOT}" > /etc/apache2/sites-available/000-default.conf

# Enable mod_rewrite
RUN a2enmod rewrite

# Run Laravel migrations
CMD php artisan migrate --force && apache2-foreground
