#!/usr/bin/env bash
set -e

composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force