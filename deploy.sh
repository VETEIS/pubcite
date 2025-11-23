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
echo "Database config check:"
echo "  DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "  DB_HOST: ${DB_HOST:-not set}"
echo "  DB_PORT: ${DB_PORT:-not set}"
echo "  DB_DATABASE: ${DB_DATABASE:-not set}"
echo "  DB_USERNAME: ${DB_USERNAME:-not set}"

max_attempts=30
attempt=1

# First, verify Laravel can bootstrap before testing database
echo "Verifying Laravel can bootstrap..."
if ! php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php';" > /dev/null 2>&1; then
    echo "⚠️  Laravel cannot bootstrap - regenerating autoloader..."
    composer dump-autoload --optimize --no-interaction --no-scripts
    if ! php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php';" > /dev/null 2>&1; then
        echo "❌ Laravel still cannot bootstrap after regenerating autoloader"
        echo "   This is a critical error - check vendor directory and Laravel installation"
        exit 1
    fi
    echo "✅ Laravel bootstrap verified after autoloader regeneration"
else
    echo "✅ Laravel bootstrap verified"
fi

while [ $attempt -le $max_attempts ]; do
    # Now test database connection using artisan
    if php artisan db:show > /dev/null 2>&1; then
        echo "✅ Database connection established"
        break
    elif php artisan migrate:status > /dev/null 2>&1; then
        echo "✅ Database connection established (via migrate:status)"
        break
    else
        echo "Attempt $attempt/$max_attempts: Database not ready yet..."
        if [ $attempt -eq 5 ] || [ $attempt -eq 15 ] || [ $attempt -eq 25 ]; then
            echo "   Debug: Testing basic database connectivity..."
            # Test basic PostgreSQL connectivity without Laravel
            if command -v psql >/dev/null 2>&1; then
                PGPASSWORD="${DB_PASSWORD}" psql -h "${DB_HOST}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME}" -d "${DB_DATABASE}" -c "SELECT 1;" > /dev/null 2>&1 && \
                    echo "   ✓ Direct PostgreSQL connection successful" || \
                    echo "   ✗ Direct PostgreSQL connection failed"
            else
                # Use PHP PDO directly without Laravel
                php -r "
                    try {
                        \$host = getenv('DB_HOST');
                        \$port = getenv('DB_PORT') ?: '5432';
                        \$db = getenv('DB_DATABASE');
                        \$user = getenv('DB_USERNAME');
                        \$pass = getenv('DB_PASSWORD');
                        if (\$host && \$db && \$user) {
                            \$dsn = \"pgsql:host=\$host;port=\$port;dbname=\$db\";
                            \$pdo = new PDO(\$dsn, \$user, \$pass);
                            \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            \$pdo->query('SELECT 1');
                            echo '   ✓ Direct PDO connection successful' . PHP_EOL;
                        } else {
                            echo '   ✗ Database credentials incomplete' . PHP_EOL;
                        }
                    } catch (Exception \$e) {
                        echo '   ✗ Connection failed: ' . \$e->getMessage() . PHP_EOL;
                    }
                " 2>&1 || echo "   Could not test database connection"
            fi
        fi
        sleep 2
        attempt=$((attempt + 1))
    fi
done

if [ $attempt -gt $max_attempts ]; then
    echo "⚠️  Could not verify database connection after $max_attempts attempts"
    echo "   Continuing anyway - database may not be available yet"
    echo "   The application will retry database connections on first request"
    # Don't exit - allow the app to start and handle DB connection errors gracefully
fi

# Verify Laravel can bootstrap before running artisan commands
echo "Verifying Laravel bootstrap..."
if php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; echo 'OK';" 2>/dev/null; then
    echo "✅ Laravel bootstrap verified"
else
    echo "⚠️  Laravel bootstrap check failed - regenerating autoloader..."
    composer dump-autoload --optimize --no-interaction --no-scripts
    if ! php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; echo 'OK';" 2>/dev/null; then
        echo "❌ Laravel still cannot bootstrap - check vendor directory and autoloader"
        exit 1
    fi
fi

# Clear all caches first (only if Laravel can bootstrap)
echo "Clearing application caches..."
php artisan cache:clear 2>&1 || echo "⚠️  cache:clear failed (non-critical)"
php artisan config:clear 2>&1 || echo "⚠️  config:clear failed (non-critical)"
php artisan route:clear 2>&1 || echo "⚠️  route:clear failed (non-critical)"
php artisan view:clear 2>&1 || echo "⚠️  view:clear failed (non-critical)"

# Run database migrations (only if database is available)
echo "Running database migrations..."
if php artisan migrate --force 2>&1; then
    echo "✅ Database migrations completed successfully"
else
    echo "⚠️  Database migrations failed or database not available"
    echo "   Migrations will be retried on next deployment or app start"
    # Don't exit - allow app to start without migrations
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