# Use the official PHP image with required extensions
FROM php:8.2-cli

# Install system dependencies
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN apt-get update && apt-get install -y libpq-dev \
&& docker-php-ext-install pdo_pgsql pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy existing application directory contents
COPY . /app

# Install Node.js (for Vite asset build)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install NPM dependencies and build assets
RUN npm install
RUN npm run build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 10000 for Render
EXPOSE 10000

# Start the Laravel server, run migrations, and seed the database
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host 0.0.0.0 --port 10000

RUN chmod -R 755 /app/public/build 