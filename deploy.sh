#!/bin/bash

# Don't exit on error - we want to continue even if some steps fail
set +e

echo "Starting deployment process..."

# Check if we're in a production environment
if [ "$APP_ENV" = "production" ]; then
    echo "Production environment detected"
    
    # Only build Vite assets if they don't exist (they should be built in Dockerfile)
    if [ ! -d "public/build" ] || [ -z "$(ls -A public/build 2>/dev/null)" ]; then
        echo "Building Vite assets..."
        npm run build
        
        # Verify assets were built
        if [ -d "public/build" ]; then
            echo "✅ Vite assets built successfully"
            ls -la public/build/
        else
            echo "⚠️  Vite assets build failed, but continuing..."
        fi
    else
        echo "✅ Vite assets already exist, skipping build"
    fi
fi

# Check database environment variables
echo "Checking database configuration..."
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "DB_PORT: ${DB_PORT:-not set}"
echo "DB_DATABASE: ${DB_DATABASE:-not set}"
echo "DB_USERNAME: ${DB_USERNAME:-not set}"
echo "DB_PASSWORD: ${DB_PASSWORD:+set (hidden)}${DB_PASSWORD:-not set}"

# Wait for database to be ready
echo "Waiting for database connection..."
max_attempts=30
attempt=1
db_ready=false

while [ $attempt -le $max_attempts ]; do
    # Check if artisan file exists first
    if [ ! -f artisan ]; then
        echo "❌ CRITICAL: artisan file not found!"
        echo "Cannot test database connection without artisan file."
        break
    fi
    
    # Try to connect and capture error output
    db_test_output=$(php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'SUCCESS'; } catch (\Exception \$e) { echo 'ERROR: ' . \$e->getMessage(); }" 2>&1)
    
    if echo "$db_test_output" | grep -q "SUCCESS"; then
        echo "✅ Database connection established"
        db_ready=true
        break
    else
        # Extract error message from output
        error_msg=$(echo "$db_test_output" | grep -i "error\|exception\|could not\|failed" | head -1 || echo "Connection failed (no error details)")
        if [ -z "$error_msg" ] || [ "$error_msg" = "Connection failed (no error details)" ]; then
            # Try a simpler connection test
            simple_test=$(php artisan tinker --execute="DB::connection()->getPdo();" 2>&1 | tail -1)
            error_msg="$simple_test"
        fi
        echo "Attempt $attempt/$max_attempts: $error_msg"
        sleep 2
        attempt=$((attempt + 1))
    fi
done

if [ "$db_ready" = false ]; then
    echo "❌ CRITICAL: Failed to connect to database after $max_attempts attempts"
    echo "Database connection is required for the application to function."
    echo "Please check:"
    echo "  1. Database service is running on Render"
    echo "  2. Database environment variables are set correctly"
    echo "  3. Database is accessible from this service"
    echo "  4. Database credentials are correct"
    echo ""
    if [ -f artisan ]; then
        echo "Attempting to show detailed error..."
        php artisan tinker --execute="try { DB::connection()->getPdo(); } catch (\Exception \$e) { echo 'Error: ' . \$e->getMessage() . PHP_EOL; echo 'Code: ' . \$e->getCode() . PHP_EOL; }" 2>&1 || true
    else
        echo "⚠️  Cannot show detailed error - artisan file not found"
    fi
    echo ""
    echo "⚠️  Starting server anyway, but application will not function without database."
fi

# Clear all caches first
echo "Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run database migrations (only if DB is ready)
if [ "$db_ready" = true ]; then
    echo "Running database migrations..."
    if php artisan migrate --force; then
        echo "✅ Database migrations completed successfully"
    else
        echo "⚠️  Database migrations failed, but continuing..."
    fi
    
    # Seed the database if needed
    if [ "$SEED_DATABASE" = "true" ]; then
        echo "Seeding database..."
        if php artisan db:seed --force; then
            echo "✅ Database seeded successfully"
        else
            echo "⚠️  Database seeding failed, but continuing..."
        fi
    fi
else
    echo "⚠️  Skipping database migrations - database not ready"
fi

# Optimize the application (only if DB is ready, as config:cache needs DB)
if [ "$db_ready" = true ]; then
    echo "Optimizing application..."
    php artisan config:cache || echo "⚠️  Config cache failed, but continuing..."
    php artisan route:cache || echo "⚠️  Route cache failed, but continuing..."
    php artisan view:cache || echo "⚠️  View cache failed, but continuing..."
else
    echo "⚠️  Skipping optimization - database not ready"
    # Clear caches instead
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

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

# Final check before starting server
if [ "$db_ready" = false ]; then
    echo ""
    echo "⚠️  WARNING: Starting server without database connection."
    echo "The application will not function properly until database is connected."
    echo "Check Render dashboard to ensure:"
    echo "  1. A PostgreSQL database service is created"
    echo "  2. The database service is linked to this web service"
    echo "  3. Environment variables are automatically injected"
    echo ""
fi

# Start the web server (this must succeed - it's the main process)
echo "Starting web server on port ${PORT:-10000}..."
exec php -S 0.0.0.0:${PORT:-10000} -t public 