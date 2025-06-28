#!/bin/bash

# Exit on any error
set -e

echo "Starting deployment process..."

# Generate application key if not exists
php artisan key:generate --no-interaction

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Optimize for production
php artisan optimize

echo "Deployment completed successfully!"

# Start the server
php artisan serve --host 0.0.0.0 --port 10000 