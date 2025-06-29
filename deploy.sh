#!/bin/bash

# Exit on any error
set -e

echo "Starting deployment process..."

# Check if we're in a production environment
if [ "$APP_ENV" = "production" ]; then
    echo "Production environment detected"
    
    # Build Vite assets for production
    echo "Building Vite assets..."
    npm run build
    
    # Verify assets were built
    if [ -d "public/build" ]; then
        echo "✅ Vite assets built successfully"
        ls -la public/build/
    else
        echo "❌ Vite assets build failed"
        exit 1
    fi
fi

# Wait for database to be ready
echo "Waiting for database connection..."
max_attempts=30
attempt=1

while [ $attempt -le $max_attempts ]; do
    if php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
        echo "✅ Database connection established"
        break
    else
        echo "Attempt $attempt/$max_attempts: Database not ready yet..."
        sleep 2
        attempt=$((attempt + 1))
    fi
done

if [ $attempt -gt $max_attempts ]; then
    echo "❌ Failed to connect to database after $max_attempts attempts"
    exit 1
fi

# Clear all caches first
echo "Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run database migrations
echo "Running database migrations..."
if php artisan migrate --force; then
    echo "✅ Database migrations completed successfully"
else
    echo "❌ Database migrations failed"
    exit 1
fi

# Seed the database if needed
if [ "$SEED_DATABASE" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
    echo "✅ Database seeded successfully"
fi

# Optimize the application
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "Setting file permissions..."
chmod -R 755 storage bootstrap/cache public/build
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Deployment completed successfully!"

# Start the web server
echo "Starting web server..."
php -S 0.0.0.0:10000 -t public 