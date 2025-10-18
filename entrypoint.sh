#!/bin/bash
set -e

echo "Starting application setup..."

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:CHANGEME" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders (optional - uncomment if you want auto-seeding)
if [ "$SEED_DATABASE" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed
fi

echo "Application ready!"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/app.conf
