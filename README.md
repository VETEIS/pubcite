# PubCite - USeP Publication Unit System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-12+-blue.svg)](https://postgresql.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4+-38B2AC.svg)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.13+-77C3D8.svg)](https://alpinejs.dev)
[![Livewire](https://img.shields.io/badge/Livewire-3.0+-4E56A6.svg)](https://livewire.laravel.com)
[![Vite](https://img.shields.io/badge/Vite-6.2+-646CFF.svg)](https://vitejs.dev)
[![Laravel Jetstream](https://img.shields.io/badge/Laravel_Jetstream-5.3+-FF2D20.svg)](https://jetstream.laravel.com)
[![Laravel Sanctum](https://img.shields.io/badge/Laravel_Sanctum-4.0+-FF2D20.svg)](https://laravel.com/docs/sanctum)
[![Laravel Socialite](https://img.shields.io/badge/Laravel_Socialite-5.21+-FF2D20.svg)](https://laravel.com/docs/socialite)
[![PhpWord](https://img.shields.io/badge/PhpWord-1.4+-00B4DB.svg)](https://github.com/PHPOffice/PHPWord)
[![FPDF/FPDI](https://img.shields.io/badge/FPDF/FPDI-2.6+-FF6B6B.svg)](https://github.com/Setasign/FPDF)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive web application for the University of Southeastern Philippines (USeP) Publication Unit, designed to streamline publication and citation incentive applications, document management and tracking, and implement a multi-stage digital signature workflow. Features full mobile responsiveness, privacy compliance, and role-based access control.

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Workflow System](#workflow-system)
- [User Roles & Permissions](#user-roles--permissions)
- [Document Types](#document-types)
- [Deployment](#deployment)
- [Development](#development)
- [Project Structure](#project-structure)
- [API Endpoints](#api-endpoints)
- [Troubleshooting](#troubleshooting)

## Features

### Core Functionality

- **Publication Incentive Applications** - Submit and manage publication incentive requests with form data validation
- **Citation Incentive Applications** - Handle citation-based incentive submissions with indexing evidence
- **Draft Management** - Save and resume incomplete applications with session-based draft storage
- **Document Generation** - Dynamic DOCX/PDF generation from templates using PhpWord
- **Multi-Stage Signature Workflow** - 5-stage approval process with digital signatures
- **Real-time Dashboard** - Live updates, progress tracking, and comprehensive analytics
- **Activity Logging** - Detailed audit trails for all system actions
- **Researcher Profiles** - Public researcher showcase on landing page with profile links

### Authentication & Security

- **Google OAuth Integration** - Seamless login with USeP Google accounts (@usep.edu.ph domain)
- **Traditional Login** - Email/password authentication for non-Google users
- **Two-Factor Authentication** - Enhanced security with 2FA support via Laravel Fortify
- **Privacy Compliance** - Mandatory data privacy agreement acceptance before system access
- **Role-based Access Control** - Granular permissions for Admin, User, and Signatory roles
- **Mobile Security** - Admin accounts restricted to desktop access only
- **URL Injection Protection** - Server-side mobile device detection and route protection
- **CSRF Protection** - Token-based request validation
- **Rate Limiting** - Protection against abuse and spam (throttling on routes)
- **Secure File Storage** - Local storage with optional S3 configuration

### Document Management

- **Template-Based Generation** - DOCX templates with variable substitution using PhpWord
- **PDF Conversion** - Automatic DOCX to PDF conversion using LibreOffice (via DocxToPdfConverter service)
- **Template Caching** - Optimized template loading with TemplateCacheService
- **Document Preview** - Preview generated documents before submission
- **File Upload Validation** - Secure file type and size validation
- **Manual Document Signing** - Users upload signed PDF/DOCX documents (manual signing process)

### Signature System

- **Signature Tracking** - Track which signatories have signed each request
- **24-Hour Reversion Window** - Signatories can undo their signatures within 24 hours of signing
- **Workflow Integration** - Automatic workflow state progression when signed documents are uploaded
- **Signature Validation** - Automatic uppercase conversion for signatory names

### Email Notifications

- **Signatory Notifications** - Notify signatories when documents require their signature
- **Nudge Notifications** - Remind signatories of pending signatures
- **Queue-Based Sending** - Asynchronous email processing for better performance

### Admin Features

- **Comprehensive Dashboard** - Real-time statistics, charts, and activity monitoring
- **User Management** - Create, edit, and manage user accounts with role assignment
- **Request Management** - Review, download, and track all applications
- **System Settings** - Configure application parameters, director emails, and announcements
- **File Management** - Secure file downloads with access control
- **Activity Logs** - View detailed audit trails of all system actions
- **Landing Page Management** - Configure announcements and researcher profiles

### Mobile Experience

- **Fully Responsive Design** - Optimized for all device sizes (mobile, tablet, desktop)
- **Desktop-First Approach** - Built for desktop with responsive mobile support
- **Touch-Friendly Interface** - Optimized buttons, dropdowns, and interactions
- **Mobile Restrictions** - Publication and Citation forms restricted on mobile devices
- **Admin Desktop-Only** - Admin accounts must use desktop devices for full functionality

## Technology Stack

### Backend

- **Laravel 12.x** - Modern PHP web framework
- **PostgreSQL 12+** - Primary relational database
- **Laravel Jetstream 5.3** - Authentication scaffolding with Livewire
- **Laravel Sanctum 4.0** - API token authentication
- **Laravel Socialite 5.21** - OAuth provider integration
- **Laravel Fortify** - Two-factor authentication
- **Spatie ResponseCache 7.7** - HTTP response caching

### Frontend

- **Tailwind CSS 3.4** - Utility-first CSS framework
- **Alpine.js 3.13** - Lightweight JavaScript framework for reactive components
- **Livewire 3.0** - Full-stack framework for dynamic UIs without JavaScript
- **Vite 6.2** - Modern build tool and asset bundler
- **Hotwired Turbo 8.0** - SPA-like navigation without full page reloads
- **Chart.js** - Data visualization (loaded via CDN)

### Document Processing

- **PhpWord 1.4** - Microsoft Word document generation and template processing
- **LibreOffice** - DOCX to PDF conversion (required for PDF generation)
- **DomPDF 3.1** - PDF generation library (installed but not actively used in current implementation)

### Services & Architecture

- **DocumentGenerationService** - Handles DOCX generation from templates
- **DocxToPdfConverter** - Converts DOCX files to PDF format using LibreOffice command-line tool
- **TemplateCacheService** - Caches and optimizes template loading
- **TemplatePreloader** - Preloads templates for faster generation
- **RecaptchaService** - Google reCAPTCHA integration

## Installation

### Prerequisites

Before setting up the project, ensure you have the following installed:

- **PHP 8.2+** with extensions: `pdo_pgsql`, `zip`, `mbstring`, `xml`, `gd`
- **Composer** 2.x
- **Node.js** 20.x and npm
- **PostgreSQL** 12+
- **LibreOffice** - Required for DOCX to PDF conversion (install from [LibreOffice.org](https://www.libreoffice.org/))
- **Git** (optional, for version control)

#### Installing Prerequisites

**macOS:**

1. **Install Homebrew** (if not already installed)
   ```bash
   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
   ```

2. **Install PHP 8.2+ and required extensions**
   ```bash
   brew install php@8.2
   brew link php@8.2
   brew install pkg-config
   pecl install zip
   ```

3. **Install PostgreSQL**
   ```bash
   brew install postgresql@14
   brew services start postgresql@14
   ```

4. **Install Node.js 20.x**
   ```bash
   brew install node@20
   brew link node@20
   ```

5. **Install Composer**
   ```bash
   brew install composer
   ```

6. **Install LibreOffice**
   ```bash
   brew install --cask libreoffice
   ```

**Windows:**

1. **Install PHP 8.2+**
   - Download from [php.net](https://windows.php.net/download/)
   - Extract to `C:\php`
   - Add `C:\php` to your system PATH
   - Enable extensions in `php.ini`: `pdo_pgsql`, `zip`, `mbstring`, `xml`, `gd`

2. **Install Composer**
   - Download from [getcomposer.org](https://getcomposer.org/download/)
   - Run the installer and follow the setup wizard

3. **Install PostgreSQL**
   - Download from [postgresql.org](https://www.postgresql.org/download/windows/)
   - Run the installer and remember the password you set for the `postgres` user

4. **Install Node.js 20.x**
   - Download from [nodejs.org](https://nodejs.org/)
   - Run the installer (includes npm)

5. **Install LibreOffice**
   - Download from [libreoffice.org](https://www.libreoffice.org/download/download/)
   - Run the installer

**Verify installations:**
```bash
php -v          # Should show PHP 8.2+
composer -V     # Should show Composer version
node -v         # Should show Node.js 20.x
npm -v          # Should show npm version
psql --version  # Should show PostgreSQL version
```

### Local Development Setup

1. **Clone or copy the repository**
   ```bash
   # If using Git
   git clone <repository-url> pubcite
   cd pubcite
   
   # Or copy the project folder to your desired location
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database in `.env`**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=pubcite
   DB_USERNAME=your_username    # On macOS: usually your Mac username (no password)
                                # On Windows: usually 'postgres' (with password you set)
   DB_PASSWORD=your_password    # On macOS: leave empty for local PostgreSQL
                                # On Windows: password you set during PostgreSQL installation
   ```

6. **Create PostgreSQL database**
   ```bash
   # macOS/Linux
   createdb pubcite
   
   # Or using psql (both platforms):
   psql postgres
   CREATE DATABASE pubcite;
   \q
   
   # Windows (if createdb doesn't work):
   # Open pgAdmin or use psql from PostgreSQL bin directory
   ```

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **(Optional) Seed database**
   ```bash
   php artisan db:seed
   ```

9. **Create storage link**
   ```bash
   php artisan storage:link
   ```

10. **Set proper permissions**
    ```bash
    # macOS/Linux
    chmod -R 775 storage bootstrap/cache
    
    # Windows (run in PowerShell as Administrator if needed)
    icacls storage /grant Users:F /T
    icacls bootstrap\cache /grant Users:F /T
    ```

11. **Build assets**
    ```bash
    npm run build
    ```

12. **Start development server**
    ```bash
    # Option A: Run separately (recommended for first time)
    # Terminal 1 - Laravel server
    php artisan serve
    
    # Terminal 2 - Vite dev server (for hot reloading)
    npm run dev
    
    # Option B: Run all together (using composer script)
    composer run dev
    # This runs server, queue, logs, and vite all together
    ```

13. **Access the application**
    ```
    http://localhost:8000
    ```

## Configuration

### Environment Variables

Key environment variables to configure:

```env
# Application
APP_NAME="PubCite"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pubcite
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# reCAPTCHA (optional)
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@usep.edu.ph
MAIL_FROM_NAME="${APP_NAME}"

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Queue (for email notifications)
QUEUE_CONNECTION=database

# File Storage
FILESYSTEM_DISK=local

# Cache
CACHE_DRIVER=file
```

### Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable Google+ API
4. Create OAuth 2.0 credentials (Web application)
5. Add authorized redirect URI: `http://localhost:8000/auth/google/callback` (for local) or `https://your-domain.com/auth/google/callback` (for production)
6. Restrict to `@usep.edu.ph` domain in OAuth consent screen
7. Copy Client ID and Secret to `.env`

### File Storage Configuration

**Local Storage (Default):**
- Files are stored in `storage/app/private/` (private files) and `storage/app/public/` (public files)
- Run `php artisan storage:link` to create symbolic link for public files
- Access public files via `/storage/` URL

**AWS S3 (Optional):**
1. Install AWS SDK: `composer require league/flysystem-aws-s3-v3 "^3.0"`
2. Update `config/filesystems.php` to add S3 disk configuration
3. Set `FILESYSTEM_DISK=s3` in `.env`
4. Configure AWS credentials in `.env`:
   ```env
   AWS_ACCESS_KEY_ID=your-access-key
   AWS_SECRET_ACCESS_KEY=your-secret-key
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=your-bucket-name
   ```

### System Settings

Configure system settings via Admin Panel → Settings:
- **Director Emails** - Deputy Director and RDD Director email addresses
- **Official Names** - Official names for directors
- **Landing Page Announcements** - Manage announcements on the welcome page
- **Researcher Profiles** - Configure researcher profiles displayed on landing page

## Workflow System

The application implements a **5-stage signature workflow** for request approval:

### Workflow States

1. **`pending_user_signature`** - Initial state, user must upload signed documents for their own request
2. **`pending_research_manager`** - Awaiting Research Center Manager to upload signed documents
3. **`pending_dean`** - Awaiting College Dean to upload signed documents
4. **`pending_deputy_director`** - Awaiting Deputy Director to upload signed documents
5. **`pending_director`** - Awaiting RDD Director to upload signed documents
6. **`completed`** - All signatures collected, request fully approved

### Workflow Progression

- Workflow state automatically progresses when the appropriate signatory uploads signed documents (PDF/DOCX)
- Each stage requires a specific signatory type to upload signed documents before moving to the next stage
- Users can track progress via the dashboard showing signature progress (e.g., "2/5")
- Signature status is tracked in the `request_signatures` table
- **Manual Signing Process**: Signatories download documents, sign them manually (outside the system), then upload the signed versions

### Signatory Types

- **`user`** - The request submitter (signs first)
- **`center_manager`** - Research Center Manager
- **`college_dean`** - College Dean
- **`deputy_director`** - Deputy Director (configured via settings)
- **`rdd_director`** - RDD Director (configured via settings)

## User Roles & Permissions

### Admin Role

- **Full system access** (desktop only)
- Create, edit, and delete users
- Manage all requests
- Configure system settings
- View activity logs
- Download files
- Manage researcher profiles and announcements

### User Role

- Submit publication and citation requests
- Upload and manage personal signatures
- View own requests and track progress
- Sign own requests (first stage)
- Upload required documents
- Save drafts and resume later

### Signatory Role

- View pending requests requiring their signature
- Sign documents assigned to their signatory type
- View signature history
- Receive email notifications for pending signatures

**Signatory Types:**
- `faculty` - Faculty member (for future use)
- `center_manager` - Research Center Manager
- `college_dean` - College Dean
- `deputy_director` - Deputy Director
- `rdd_director` - RDD Director

## Document Types

### Publication Documents

1. **Incentive Application Form** (`Incentive_Application_Form.docx`)
   - Generated from publication request form data
   - Includes researcher information, publication details, indexing evidence

2. **Recommendation Letter** (`Recommendation_Letter_Form.docx`)
   - Generated for publication recommendations
   - Includes college header, faculty details, citation information

### Citation Documents

1. **Incentive Application Form** (`Cite_Incentive_Application.docx`)
   - Generated from citation request form data
   - Includes citation details, indexing evidence

2. **Recommendation Letter** (`Cite_Recommendation_Letter.docx`)
   - Generated for citation recommendations

### Terminal Report

- **Terminal Report Form** (`Terminal_Report_Form.docx`)
  - Used for terminal reports (uploaded by users)

## Deployment

### Production Checklist

1. **Update environment variables**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```

2. **Build production assets**
   ```bash
   npm run build
   composer install --optimize-autoloader --no-dev
   ```

3. **Run migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Cache configuration**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Set proper permissions**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

6. **Configure queue worker** (for email notifications)
   ```bash
   php artisan queue:work --tries=3
   ```

### Render.com Deployment

1. Connect your GitHub repository to Render
2. Use the provided `render.yaml` configuration
3. Set all environment variables in Render dashboard
4. Configure PostgreSQL database
5. Deploy automatically on push to main branch

### Docker Deployment

1. **Build the Docker image**
   ```bash
   docker build -t pubcite .
   ```

2. **Run the container**
   ```bash
   docker run -p 10000:10000 \
     -e DB_HOST=your-db-host \
     -e DB_DATABASE=pubcite \
     -e DB_USERNAME=your-username \
     -e DB_PASSWORD=your-password \
     pubcite
   ```

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

```bash
./vendor/bin/pint
```

### Database Seeding

```bash
php artisan db:seed
```

### Artisan Commands

**Cleanup Commands:**
```bash
# Clean up temporary files
php artisan cleanup:temp-files

# Clean up orphaned directories
php artisan cleanup:orphaned-directories

# Clean up preview cache
php artisan cleanup:preview-cache

# Clean up stale lock files
php artisan cleanup:stale-lock-files

# Clean up legacy directories
php artisan cleanup:legacy-directories
```

**Debug Commands:**
```bash
# Debug user roles
php artisan debug:user-roles
```

**Migration Commands:**
```bash
# Migrate existing signatures
php artisan migrate:existing-signatures

# Update Google users auth provider
php artisan update:google-users-auth-provider
```

### Development Workflow

1. **Start all services**
   ```bash
   composer run dev
   ```
   This runs:
   - Laravel server (port 8000)
   - Queue worker
   - Pail (log viewer)
   - Vite dev server

2. **Make changes** to code, views, or assets

3. **Hot reloading** - Vite automatically reloads on asset changes

4. **View logs** - Check Pail output or `storage/logs/laravel.log`

## Project Structure

```
pubcite/
├── app/
│   ├── Actions/
│   │   ├── Fortify/          # Fortify actions (user creation, password updates)
│   │   └── Jetstream/        # Jetstream actions (user deletion)
│   ├── Console/
│   │   └── Commands/         # Artisan commands (cleanup, debug, migration)
│   ├── Enums/
│   │   └── SignatureStatus.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/        # Admin controllers
│   │   │   ├── Auth/         # Authentication controllers
│   │   │   └── Traits/       # Controller traits (DraftSessionManager)
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── MobileRestriction.php
│   │   │   ├── PrivacyAcceptanceMiddleware.php
│   │   │   └── SecurityHeaders.php
│   │   └── Requests/         # Form request validation
│   ├── Jobs/                 # Queue jobs
│   ├── Livewire/             # Livewire components
│   ├── Mail/                 # Email notification classes
│   ├── Models/                # Eloquent models
│   ├── Policies/             # Authorization policies
│   ├── Providers/            # Service providers
│   ├── Services/            # Business logic services
│   ├── Traits/               # Reusable traits
│   └── View/
│       └── Components/       # Blade components
├── bootstrap/
│   ├── app.php              # Application bootstrap
│   └── providers.php        # Service provider registration
├── config/                   # Configuration files
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── public/                  # Public assets and index.php
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   ├── markdown/           # Markdown files (policy, terms)
│   ├── templates/          # DOCX templates
│   └── views/              # Blade templates
├── routes/
│   ├── api.php             # API routes
│   ├── console.php         # Console routes
│   └── web.php             # Web routes
├── storage/
│   ├── app/
│   │   ├── private/        # Private file storage
│   │   ├── public/         # Public file storage
│   │   └── templates/      # Template files
│   └── logs/               # Application logs
├── tests/                   # Test suites
├── .env.example            # Environment template
├── composer.json           # PHP dependencies
├── package.json            # Node.js dependencies
├── vite.config.js          # Vite configuration
└── tailwind.config.js      # Tailwind CSS configuration
```

## API Endpoints

### Public Endpoints

- `GET /api/announcements` - Get landing page announcements
- `GET /api/researchers` - Get active researcher profiles

### Authenticated Endpoints

- `GET /api/drafts` - Get user's drafts
- `GET /api/draft/{draft}` - Get specific draft
- `DELETE /drafts/{draft}` - Delete a draft

### Admin Endpoints

- `GET /admin/dashboard/data` - Get dashboard statistics
- `GET /admin/requests/{request}/data` - Get request data
- `GET /admin/requests/{request}/download-zip` - Download request files as ZIP
- `GET /admin/download/{type}/{filename}` - Download admin files

## Troubleshooting

### Common Issues

**Error 419 (CSRF Token Mismatch)**
- **Cause:** Session expired or CSRF token mismatch
- **Solution:** Refresh the page to get a new CSRF token
- **Prevention:** Notice displayed in document generation section

**Database Connection Errors**
- **macOS:** Check PostgreSQL is running: `brew services list` or `brew services start postgresql@14`
- **Windows:** Check PostgreSQL service in Services (services.msc) or start it via pgAdmin
- Verify database credentials in `.env`
- Ensure database exists: `psql -l | grep pubcite` (macOS/Linux) or check in pgAdmin (Windows)
- **macOS:** PostgreSQL default user is usually your Mac username with no password
- **Windows:** PostgreSQL default user is usually `postgres` with the password you set during installation

**Permission Errors**
- **macOS/Linux:** Run: `chmod -R 775 storage bootstrap/cache`
- **Windows:** Run in PowerShell: `icacls storage /grant Users:F /T` and `icacls bootstrap\cache /grant Users:F /T`
- Ensure web server user has write access

**Template Not Found Errors**
- Verify templates exist in `storage/app/templates/`
- Check file permissions on template files
- Run: `php artisan storage:link`

**Email Not Sending**
- Check queue worker is running: `php artisan queue:work`
- Verify mail configuration in `.env`
- Check `storage/logs/laravel.log` for email errors

**Document Generation Fails**
- Check PHP extensions: `php -m | grep zip` (macOS/Linux) or `php -m | findstr zip` (Windows)
- Verify template files are valid DOCX files
- Check storage permissions: `ls -la storage/app/templates/` (macOS/Linux) or check in File Explorer (Windows)
- Ensure LibreOffice is installed and accessible from command line

**Mobile Restrictions Not Working**
- Verify `MobileRestriction` middleware is applied to routes
- Check User-Agent detection in middleware
- Test with different mobile devices/browsers

### Debugging

**View Logs**
```bash
# Real-time log viewing
php artisan pail

# Or tail the log file
# macOS/Linux:
tail -f storage/logs/laravel.log

# Windows (PowerShell):
Get-Content storage\logs\laravel.log -Wait -Tail 50
```

**Clear Caches**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Reset Application**
```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
```

**Additional Troubleshooting**

**If you get "Class not found" errors:**
```bash
composer dump-autoload
```

**If you get "PDO extension not found" (macOS):**
```bash
# Check which PHP you're using
which php

# Make sure you're using Homebrew PHP
brew link --overwrite php@8.2
```

**If npm install fails:**
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rm -rf node_modules package-lock.json  # macOS/Linux
# or on Windows: rmdir /s node_modules & del package-lock.json
npm install
```

**If composer install fails:**
```bash
# Clear composer cache
composer clear-cache

# Reinstall
composer install --no-cache
```

**Port conflicts:**
- If port 8000 is busy, use: `php artisan serve --port=8001`

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions:
- Create an issue in the GitHub repository
- Contact the development team
- Check the documentation

## About USeP

The University of Southeastern Philippines (USeP) is a premier state university in the Philippines, committed to excellence in education, research, and community service. The Publication Unit supports faculty and researchers in their academic publishing endeavors.

---

**Developed for the University of Southeastern Philippines - Obrero** - @vete
