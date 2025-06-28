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

# Check if database exists and is accessible
echo "Checking database connection..."
if php artisan tinker --execute="echo 'Database connection successful';"; then
    echo "Database connection successful!"
else
    echo "Database connection failed. Please check your database configuration."
    exit 1
fi

# Run migrations with better error handling
echo "Running database migrations..."
if php artisan migrate --force; then
    echo "Migrations completed successfully!"
else
    echo "Migration failed. Attempting to reset and migrate fresh..."
    php artisan migrate:fresh --force
fi

# Run seeders
echo "Running database seeders..."
php artisan db:seed --force

# Optimize for production
php artisan optimize

echo "Deployment completed successfully!"

# Start the server
php artisan serve --host 0.0.0.0 --port 10000 