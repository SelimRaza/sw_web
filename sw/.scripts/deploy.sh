#!/bin/bash
set -e

echo "Deployment started ..."

php artisan down  || true

# Pull the latest version of the app
git pull origin main --ff
php artisan up

# Clear the old cache
php artisan cache:clear
php artisan clear-compiled
php artisan optimize:clear

echo "Deployment finished!"
