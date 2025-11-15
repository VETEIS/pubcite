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

# Create storage link for public files
echo "Creating storage link..."
php artisan storage:link

# Verify LibreOffice installation
echo "Verifying LibreOffice installation..."
if command -v libreoffice >/dev/null 2>&1; then
    echo "✅ LibreOffice found: $(libreoffice --version 2>&1 | head -n1)"
elif command -v soffice >/dev/null 2>&1; then
    echo "✅ LibreOffice found: $(soffice --version 2>&1 | head -n1)"
else
    echo "❌ LibreOffice not found - PDF conversion will fail"
    echo "Available commands:"
    which -a libreoffice soffice 2>/dev/null || echo "No LibreOffice commands found"
fi

# Set proper permissions
echo "Setting file permissions..."
chmod -R 755 storage bootstrap/cache public/build
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Deployment completed successfully!"

# Start the queue worker in the background
echo "Starting queue worker..."
# Create logs directory if it doesn't exist
mkdir -p storage/logs
# Start queue worker and log to file
php artisan queue:work --queue=emails --tries=3 --timeout=30 --sleep=3 --max-jobs=1000 >> storage/logs/queue.log 2>&1 &
QUEUE_PID=$!
echo "✅ Queue worker started (PID: $QUEUE_PID)"
echo "   Queue logs: storage/logs/queue.log"

# Function to cleanup on exit
cleanup() {
    echo "Shutting down queue worker (PID: $QUEUE_PID)..."
    kill $QUEUE_PID 2>/dev/null || true
    wait $QUEUE_PID 2>/dev/null || true
    echo "Queue worker stopped"
}
trap cleanup EXIT INT TERM

# Start the web server
echo "Starting web server..."
php -S 0.0.0.0:10000 -t public 