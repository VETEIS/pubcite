# Use the official PHP image with required extensions
FROM php:8.2-cli

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
    libmcrypt-dev \
    fonts-liberation \
    libfontconfig1 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip

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
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package.json and package-lock.json for Node.js dependencies
COPY package.json package-lock.json ./

# Install NPM dependencies (including dev dependencies for build)
RUN npm ci

# Copy the rest of the application
COPY . .

# Run composer scripts after the application is copied
RUN composer run-script post-autoload-dump

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