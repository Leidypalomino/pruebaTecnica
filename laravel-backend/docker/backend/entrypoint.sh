#!/bin/sh
set -e

# This script now runs as ROOT.

# Fix Git ownership error.
git config --global --add safe.directory /var/www

# Install Composer dependencies.
composer install --no-interaction --no-progress

# Check if the long first-time setup needs to be run.
if [ ! -f "vendor/installed.marker" ]; then
    echo "-------------------------------------------------"
    echo "FIRST TIME SETUP: This will only run once."
    echo "-------------------------------------------------"

    echo "Running Laravel setup..."
    php artisan key:generate
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear

    echo "Waiting for database..."
    while ! pg_isready -h db -p 5432 -q -U ${DB_USERNAME}; do
      >&2 echo "Postgres is unavailable - sleeping"
      sleep 1
    done
    >&2 echo "Postgres is up - executing command"

    php artisan migrate:fresh --seed --force
    php artisan l5-swagger:generate

    touch vendor/installed.marker
    echo "First time setup complete. Marker file created."
else
    echo "Setup marker found, skipping migrations and seeding."
fi

echo "Starting PHP-FPM..."

# Launch PHP-FPM directly. This will keep the container running.
exec php-fpm