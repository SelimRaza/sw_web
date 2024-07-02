#!/bin/bash
set -e

echo "Deployment started ..."

# Enter maintenance mode or return true
# if already is in maintenance mode
php artisan down  || true

# Pull the latest version of the app
git pull origin main --ff

php artisan up

# Clear the old cache
php artisan cache:clear
php artisan clear-compiled
php artisan optimize:clear

echo "Deployment finished!"
