#!/bin/sh
set -e

# Clear Laravel caches on startup
echo "Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run the default command (e.g., php-fpm or bash)
exec "$@"
