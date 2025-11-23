#!/bin/bash

# Exit on any error
set -e

# ASCII Art Banner Function
print_banner() {
    echo ""
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                                                              ║"
    echo "║  $1"
    echo "║                                                              ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo ""
}

print_banner "🚀  STARTING DEPLOYMENT PROCESS"

# Check if we're in a production environment
if [ "$APP_ENV" = "production" ]; then
    print_banner "📦  BUILDING PRODUCTION ASSETS"
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

print_banner "🔧  LARAVEL BOOTSTRAP VERIFICATION"
echo "Verifying Laravel can bootstrap before database checks..."

# First check if vendor/autoload.php exists
if [ ! -f "vendor/autoload.php" ]; then
    echo "❌ vendor/autoload.php not found - critical error"
    echo "   This means composer install didn't complete properly"
    exit 1
fi

# Check if Laravel framework is installed
if [ ! -d "vendor/laravel/framework" ]; then
    echo "❌ Laravel framework not found in vendor directory"
    echo "   This means composer install didn't complete properly"
    echo "   Attempting to reinstall dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
fi

# Try to bootstrap Laravel
if php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php';" > /dev/null 2>&1; then
    echo "✅ Laravel bootstrap verified"
else
    echo "⚠️  Laravel cannot bootstrap - investigating..."
    
    # Check if Application class file exists
    if [ ! -f "vendor/laravel/framework/src/Illuminate/Foundation/Application.php" ]; then
        echo "❌ Application.php not found - vendor directory is incomplete"
        echo "   Reinstalling Laravel framework..."
        composer require laravel/framework --no-interaction --no-scripts || true
        composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
    fi
    
    # Check if vendor directory exists and is complete
    echo "   Checking vendor directory..."
    if [ ! -d "vendor/laravel/framework" ]; then
        echo "   ❌ vendor/laravel/framework not found - reinstalling dependencies..."
        composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
    else
        echo "   ✓ vendor/laravel/framework exists"
        echo "   Checking vendor/laravel/framework size..."
        du -sh vendor/laravel/framework 2>/dev/null || echo "   Could not check size"
    fi
    
    # Try regenerating autoloader (this should include vendor classes)
    echo "   Regenerating autoloader (this should include all vendor classes)..."
    composer dump-autoload --optimize --no-interaction --no-scripts 2>&1 | grep -E "Generated|classes" || echo "   Autoloader regenerated"
    
    # Try again
    if php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php';" > /dev/null 2>&1; then
        echo "✅ Laravel bootstrap verified after autoloader regeneration"
    else
        echo "❌ Laravel still cannot bootstrap"
        echo "   Debug: Testing if Application class can be loaded..."
        php -r "require 'vendor/autoload.php'; var_dump(class_exists('Illuminate\Foundation\Application'));" 2>&1
        echo "   This is a critical error - deployment cannot continue"
        exit 1
    fi
fi

print_banner "🗄️   DATABASE CONNECTION CHECK"
echo "Waiting for database connection..."
echo "Database config check:"
echo "  DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "  DB_HOST: ${DB_HOST:-not set}"
echo "  DB_PORT: ${DB_PORT:-not set}"
echo "  DB_DATABASE: ${DB_DATABASE:-not set}"
echo "  DB_USERNAME: ${DB_USERNAME:-not set}"

max_attempts=30
attempt=1

while [ $attempt -le $max_attempts ]; do
    # Test database connection using artisan (Laravel is now bootstrapped)
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

print_banner "🧹  CLEARING APPLICATION CACHES"
echo "Clearing application caches..."
php artisan cache:clear 2>&1 || echo "⚠️  cache:clear failed (non-critical)"
php artisan config:clear 2>&1 || echo "⚠️  config:clear failed (non-critical)"
php artisan route:clear 2>&1 || echo "⚠️  route:clear failed (non-critical)"
php artisan view:clear 2>&1 || echo "⚠️  view:clear failed (non-critical)"

print_banner "📊  DATABASE MIGRATIONS"
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
    print_banner "🌱  DATABASE SEEDING"
    echo "Seeding database..."
    php artisan db:seed --force
    echo "✅ Database seeded successfully"
fi

print_banner "⚡  OPTIMIZING APPLICATION"
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_banner "🔗  CREATING STORAGE LINKS"
echo "Creating storage link..."
php artisan storage:link

print_banner "📄  VERIFYING LIBREOFFICE"
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

print_banner "🔐  SETTING FILE PERMISSIONS"
echo "Setting file permissions..."
chmod -R 755 storage bootstrap/cache public/build
chown -R www-data:www-data storage bootstrap/cache

print_banner "✅  DEPLOYMENT COMPLETED SUCCESSFULLY"

# Start the queue worker in the background
print_banner "🔄  STARTING QUEUE WORKER"
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

print_banner "🌐  STARTING WEB SERVER"
echo "Starting web server..."
php -S 0.0.0.0:10000 -t public 