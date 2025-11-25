# Use the official PHP image with required extensions
# Upgraded to PHP 8.3 to support phpspreadsheet 5.2.0 which requires zipstream-php 3.2.0 (PHP 8.3+)
FROM php:8.3-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    fonts-liberation \
    libfontconfig1 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install XML extensions required by PhpSpreadsheet
# xmlreader and xmlwriter depend on dom headers (ext/dom/dom_ce.h)
# Ensure PHP source is available, then install dom first, then xmlreader/xmlwriter
RUN set -e; \
    echo "Ensuring PHP source tree is available..."; \
    if [ ! -d "/usr/src/php/ext" ]; then \
        echo "Extracting PHP source..."; \
        docker-php-source extract || echo "⚠ Source extraction failed or already extracted"; \
    fi; \
    echo "Checking dom extension source..."; \
    if [ -d "/usr/src/php/ext/dom" ]; then \
        echo "Installing dom extension first (ensures headers are available for xmlreader/xmlwriter)..."; \
        docker-php-ext-configure dom && \
        docker-php-ext-install -j$(nproc) dom || (echo "⚠ dom install warning (may already be enabled)" && php -m | grep dom || true); \
        echo "✓ dom installation attempted"; \
    else \
        echo "⚠ /usr/src/php/ext/dom not found"; \
        echo "Listing available extensions:"; \
        ls -la /usr/src/php/ext/ | head -20; \
    fi; \
    echo "Installing xmlreader (requires dom headers)..."; \
    docker-php-ext-configure xmlreader && \
    docker-php-ext-install -j$(nproc) xmlreader && \
    echo "✓ xmlreader installed" && \
    echo "Installing xmlwriter..."; \
    docker-php-ext-configure xmlwriter && \
    docker-php-ext-install -j$(nproc) xmlwriter && \
    echo "✓ xmlwriter installed" && \
    echo "Verifying all XML extensions..." && \
    php -m | grep -E "^(dom|xml|xmlreader|xmlwriter|libxml)$" && \
    echo "✓ All XML extensions verified successfully"

# Install LibreOffice using apt-get with dependency resolution
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libreoffice \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/* \
    && rm -rf /var/tmp/*

# Verify LibreOffice installation and fix any library issues
RUN libreoffice --version || echo "LibreOffice version check failed" \
    && echo "LibreOffice installation completed"

# Install PostgreSQL extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js (for Vite asset build)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies without running scripts
# Increase memory limit for composer install
# Show verbose output to debug any extension issues
RUN echo "PHP version: $(php -v | head -1)" && \
    echo "Available extensions:" && php -m && \
    echo "Checking required extensions for PhpSpreadsheet..." && \
    (php -m | grep -q "dom" && echo "✓ dom found" || echo "✗ dom missing") && \
    (php -m | grep -q "xmlreader" && echo "✓ xmlreader found" || echo "✗ xmlreader missing") && \
    (php -m | grep -q "xmlwriter" && echo "✓ xmlwriter found" || echo "✗ xmlwriter missing") && \
    COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --verbose 2>&1 | tail -50 || (echo "❌ Composer install failed - checking error..." && exit 1) && \
    echo "Verifying autoloader after composer install..." && \
    if [ -f "vendor/autoload.php" ]; then \
        php -r "require 'vendor/autoload.php'; echo (class_exists('Illuminate\Foundation\Application') ? '✓ Autoloader OK' : '✗ Autoloader FAILED') . PHP_EOL;"; \
    else \
        echo "❌ vendor/autoload.php not found - composer install failed"; \
        exit 1; \
    fi

# Copy package.json and package-lock.json for Node.js dependencies
COPY package.json package-lock.json ./

# Install NPM dependencies (including dev dependencies for build)
RUN npm ci

# Copy the rest of the application
COPY . .

# After copying app files, regenerate autoloader to include App namespace classes
# IMPORTANT: composer dump-autoload regenerates the ENTIRE autoloader including vendor
# The vendor directory from composer install is preserved (not copied due to .dockerignore)
# We just need to regenerate to include the App namespace from the copied files
RUN echo "Verifying vendor directory exists..." && \
    ls -la vendor/ | head -5 && \
    echo "Regenerating autoloader (this includes vendor + app classes)..." && \
    composer dump-autoload --optimize --no-interaction --no-scripts && \
    echo "Verifying autoloader includes Laravel..." && \
    php -r "require 'vendor/autoload.php'; echo (class_exists('Illuminate\Foundation\Application') ? '✓ Laravel classes found' : '✗ Laravel classes missing') . PHP_EOL;" && \
    echo "✓ Autoloader ready"

# Build assets with verbose output
RUN echo "Building Vite assets..." && npm run build

# Verify assets were built
RUN ls -la public/build/ && echo "Assets built successfully!"

# Set proper permissions
RUN chmod -R 755 /app/storage /app/bootstrap/cache /app/public/build
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Make deployment script executable
RUN chmod +x /app/deploy.sh

# Expose port 10000 for Render
EXPOSE 10000

# Start the deployment script
CMD ["/app/deploy.sh"] 

# Increase PHP upload and post size limits
RUN echo "upload_max_filesize=20M\npost_max_size=20M" > /usr/local/etc/php/conf.d/uploads.ini 