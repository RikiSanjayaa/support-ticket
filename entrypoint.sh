#!/bin/sh

echo "Starting application setup..."

# Wait for PostgreSQL to be ready
echo "Waiting for database to be ready..."
MAX_ATTEMPTS=30
ATTEMPT=0
while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    if pg_isready -h ${DB_HOST:-postgres} -p ${DB_PORT:-5432} -U ${DB_USERNAME:-postgres} >/dev/null 2>&1; then
        echo "Database is ready!"
        break
    fi
    ATTEMPT=$((ATTEMPT + 1))
    echo "Database not ready, attempt $ATTEMPT/$MAX_ATTEMPTS..."
    sleep 1
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "Database failed to become ready after $MAX_ATTEMPTS attempts"
fi

# Remove any cached configurations from development dependencies
rm -rf /app/bootstrap/cache/*

# Clear config cache to avoid issues with dev dependencies
echo "Clearing config cache..."
php artisan config:clear || true

# Generate APP_KEY if not set or if it's a placeholder
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:CHANGEME" ] || [ "$APP_KEY" = "base64:CHANGEME_GENERATE_ME" ]; then
    echo "Generating APP_KEY..."

    # Generate random key and update .env directly
    GENERATED_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"

    # Update .env file with the generated key
    if grep -q "^APP_KEY=" /app/.env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=$GENERATED_KEY|" /app/.env
    else
        echo "APP_KEY=$GENERATED_KEY" >> /app/.env
    fi

    export APP_KEY="$GENERATED_KEY"
    echo "APP_KEY generated successfully"
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force || true

# Run seeders (optional - only if SEED_DATABASE is explicitly set to true)
# Note: Seeding requires dev dependencies (Faker) which are not included in production build
if [ "$SEED_DATABASE" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force 2>/dev/null || echo "Note: Seeding skipped (dev dependencies not available in production build)"
fi

echo "Application ready! Starting services..."

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground (will keep container alive)
exec /usr/sbin/nginx -g "daemon off;"
