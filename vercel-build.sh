#!/bin/bash

# Install composer dependencies
composer install --optimize-autoloader --no-dev

# Clear and cache config (optional, Vercel handles this)
php artisan config:cache
php artisan route:cache
