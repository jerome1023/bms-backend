#!/bin/bash
set -e

# Run migrations and seeds
php artisan migrate --force
php artisan db:seed --force

# Start PHP-FPM
exec php-fpm
