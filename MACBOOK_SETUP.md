# MacBook Setup Guide - PubCite Project

## Step 1: Install Prerequisites

### Install Homebrew (if not already installed)
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Install PHP 8.2+ and required extensions
```bash
brew install php@8.2
brew link php@8.2
brew install pkg-config
pecl install zip
```

**Note:** If `pecl` is not found, you may need to install PHP with extensions:
```bash
brew install php@8.2 pkg-config
```

### Install PostgreSQL
```bash
brew install postgresql@14
brew services start postgresql@14
```

### Install Node.js 20.x
```bash
brew install node@20
brew link node@20
```

### Install Composer
```bash
brew install composer
```

### Verify installations
```bash
php -v          # Should show PHP 8.2+
composer -V     # Should show Composer version
node -v         # Should show Node.js 20.x
npm -v          # Should show npm version
psql --version  # Should show PostgreSQL version
```

---

## Step 2: Transfer Your Project

### Option A: Using USB Drive/External Storage
1. Copy the entire `pubcite` folder to your MacBook
2. Navigate to where you copied it:
```bash
cd ~/Desktop/pubcite  # or wherever you placed it
```

### Option B: Using Git (if you have a repository)
```bash
cd ~/Desktop
git clone <your-repo-url> pubcite
cd pubcite
```

### Option C: Using Cloud Storage (Dropbox, Google Drive, etc.)
1. Upload the project folder to cloud storage from your PC
2. Download it on your MacBook
3. Navigate to the folder:
```bash
cd ~/Downloads/pubcite  # or wherever you downloaded it
```

---

## Step 3: Install Project Dependencies

### Install PHP dependencies
```bash
composer install
```

### Install Node.js dependencies
```bash
npm install
```

---

## Step 4: Environment Configuration

### Copy environment file
```bash
cp .env.example .env
```

### Generate application key
```bash
php artisan key:generate
```

### Edit .env file with your database credentials
```bash
nano .env
# or use any text editor like VS Code, TextEdit, etc.
```

**Update these important settings in `.env`:**
```env
APP_NAME="PubCite"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pubcite
DB_USERNAME=your_mac_username  # Usually your Mac username
DB_PASSWORD=                   # Leave empty for local PostgreSQL

# Google OAuth (if you have credentials)
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# reCAPTCHA (if configured)
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key
```

---

## Step 5: Database Setup

### Create PostgreSQL database
```bash
# Create database
createdb pubcite

# Or using psql:
psql postgres
CREATE DATABASE pubcite;
\q
```

### Run migrations
```bash
php artisan migrate
```

### (Optional) Seed database
```bash
php artisan db:seed
```

---

## Step 6: Storage Setup

### Create storage link
```bash
php artisan storage:link
```

### Set proper permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R $(whoami):staff storage bootstrap/cache
```

---

## Step 7: Build Assets

### Build production assets
```bash
npm run build
```

### Or run in development mode (for development)
```bash
npm run dev
```

---

## Step 8: Start the Development Server

### Option A: Run separately (recommended for first time)

**Terminal 1 - Laravel server:**
```bash
php artisan serve
```
This will start the server at `http://localhost:8000`

**Terminal 2 - Vite dev server (if using npm run dev):**
```bash
npm run dev
```

### Option B: Run all together (using composer script)
```bash
composer run dev
```
This runs server, queue, logs, and vite all together.

---

## Step 9: Access the Application

Open your browser and go to:
```
http://localhost:8000
```

---

## Troubleshooting

### If you get "Class not found" errors:
```bash
composer dump-autoload
```

### If you get permission errors:
```bash
chmod -R 775 storage bootstrap/cache
```

### If PostgreSQL connection fails:
```bash
# Check if PostgreSQL is running
brew services list

# Start PostgreSQL if not running
brew services start postgresql@14

# Check PostgreSQL is accessible
psql postgres
```

### If you get "PDO extension not found":
```bash
# Check which PHP you're using
which php

# Make sure you're using Homebrew PHP
brew link --overwrite php@8.2
```

### If npm install fails:
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

### If composer install fails:
```bash
# Clear composer cache
composer clear-cache

# Reinstall
composer install --no-cache
```

### If you need to reset everything:
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild
composer dump-autoload
npm run build
```

---

## Quick Reference Commands

```bash
# Start development
php artisan serve          # Laravel server
npm run dev                # Vite dev server

# Database
php artisan migrate        # Run migrations
php artisan migrate:fresh  # Fresh migration (drops all tables)
php artisan db:seed        # Seed database

# Cache management
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Build for production
npm run build
composer install --optimize-autoloader --no-dev
```

---

## Next Steps

1. ✅ Verify the application loads at `http://localhost:8000`
2. ✅ Test login functionality
3. ✅ Check if database connections work
4. ✅ Verify file uploads work (check `storage/app` folder)
5. ✅ Test document generation features

---

## Notes

- **PostgreSQL default user:** On macOS, PostgreSQL usually uses your Mac username as the default user with no password for local connections
- **Port conflicts:** If port 8000 is busy, use `php artisan serve --port=8001`
- **PHP extensions:** Make sure `pdo_pgsql`, `zip`, and `mbstring` are enabled (check with `php -m`)
- **Storage:** Files are stored in `storage/app/` directory
- **Logs:** Check `storage/logs/laravel.log` for errors

---

**Good luck with your MacBook setup! 🚀**


