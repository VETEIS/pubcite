# PubCite - USeP Publication Unit System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-12+-blue.svg)](https://postgresql.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4+-38B2AC.svg)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.13+-77C3D8.svg)](https://alpinejs.dev)
[![Livewire](https://img.shields.io/badge/Livewire-3.0+-4E56A6.svg)](https://livewire.laravel.com)
[![Vite](https://img.shields.io/badge/Vite-6.2+-646CFF.svg)](https://vitejs.dev)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive web application for the University of Southeastern Philippines (USeP) Publication Unit, designed to streamline publication and citation incentive applications, document management and tracking, and implement digital signature workflows. Features full mobile responsiveness, privacy compliance, and role-based access control.

## Features

### Core Functionality
- **Publication Incentive Applications** - Submit and manage publication incentive requests
- **Citation Incentive Applications** - Handle citation-based incentive submissions
- **Digital Document Management** - Secure file upload, storage, and retrieval
- **Digital Signature System** - Upload, manage, and apply personal signatures to documents
- **Real-time Dashboard** - Live updates and comprehensive analytics
- **Multi-role Access Control** - Admin, User, and Signatory roles with appropriate permissions

### Authentication & Security
- **Google OAuth Integration** - Seamless login with USeP Google accounts (@usep.edu.ph)
- **Two-Factor Authentication** - Enhanced security with 2FA support
- **Role-based Access Control** - Granular permissions for different user types
- **Privacy Compliance** - Mandatory data privacy agreement acceptance
- **Mobile Security** - Admin accounts restricted to desktop access
- **URL Injection Protection** - Server-side mobile device detection and route protection
- **Secure File Storage** - Private S3 storage with encryption
- **Rate Limiting** - Protection against abuse and spam

### Admin Features
- **Comprehensive Dashboard** - Real-time statistics and activity monitoring
- **User Management** - Create, edit, and manage user accounts
- **Request Management** - Review, approve, and track all applications
- **System Settings** - Configure application parameters
- **File Management** - Secure file downloads and management
- **Activity Logging** - Detailed audit trails for all actions

### Signature Management
- **Personal Signature Upload** - Secure signature image management
- **Document Signing** - Apply signatures to generated documents
- **Signature Reversion** - Undo signatures within 24-hour window
- **Privacy Protection** - Owner-only access to signature files
- **Multiple Signature Support** - Manage multiple signature styles

### Mobile Experience
- **Fully Responsive Design** - Optimized for all device sizes
- **Mobile-First Approach** - Progressive enhancement from mobile to desktop
- **Orientation Lock** - Portrait-only mobile experience for optimal usability
- **Mobile Dashboard** - Streamlined single-page mobile interface
- **Touch-Friendly Interface** - Optimized for touch interactions
- **Mobile Security** - Device-specific access controls and restrictions

## Technology Stack

### Backend
- **Laravel 12.x** - PHP web framework
- **PostgreSQL** - Primary database
- **Laravel Jetstream** - User interface scaffolding with authentication
- **Laravel Sanctum** - API authentication
- **Laravel Socialite** - OAuth providers
- **Spatie ResponseCache** - HTTP response caching

### Frontend
- **Tailwind CSS** - Utility-first CSS framework with responsive design
- **Alpine.js** - Lightweight JavaScript framework
- **Chart.js** - Data visualization (CDN)
- **Livewire** - Full-stack framework for dynamic UIs
- **Turbo** - Hotwired Turbo for SPA-like navigation
- **Vite** - Modern build tool and asset bundler
- **Mobile-First CSS** - Progressive enhancement for all devices
- **Glassmorphism Design** - Modern UI with backdrop blur effects

### Document Processing (wip)
- **PhpWord** - Microsoft Word document generation
- **DomPDF** - PDF generation and manipulation
- **FPDF/FPDI** - Advanced PDF processing
- **ZIP Manipulation** - Direct DOCX file processing

### Infrastructure (wip)
- **Docker** - Containerization
- **Local Storage** - File storage (configurable for S3)
- **Render** - Cloud deployment platform
- **Vite** - Asset bundling and development server

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 20.x
- PostgreSQL 12+

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/VETEIS/pubcite.git
   cd pubcite
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

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   npm run dev
   ```

### Docker Deployment

1. **Build the Docker image**
   ```bash
   docker build -t pubcite .
   ```

2. **Run the container**
   ```bash
   docker run -p 10000:10000 pubcite
   ```

## Configuration

### Environment Variables

```env
# Application
APP_NAME="PubCite"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=pubcite
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback

# File Storage (Optional - defaults to local)
FILESYSTEM_DISK=local
# AWS_ACCESS_KEY_ID=your-access-key
# AWS_SECRET_ACCESS_KEY=your-secret-key
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=your-bucket-name

# Signature Management
MAX_SIGNATURE_BYTES=1000000
SIGNATURE_MAX_DIMENSIONS=2000
SIGNATURE_RETENTION_DAYS=90

# Privacy & Security
PRIVACY_AGREEMENT_REQUIRED=true
ADMIN_MOBILE_RESTRICTED=true
MOBILE_ORIENTATION_LOCK=true
```

### Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `https://your-domain.com/auth/google/callback`
6. Restrict to `@usep.edu.ph` domain

### File Storage Configuration

**Local Storage (Default):**
- Files are stored in `storage/app/public/`
- Run `php artisan storage:link` to create symbolic link
- Access files via `/storage/` URL

**AWS S3 (Optional):**
1. Install AWS SDK: `composer require league/flysystem-aws-s3-v3`
2. Create a private S3 bucket
3. Enable server-side encryption (SSE-KMS recommended)
4. Block public access
5. Configure bucket policy to deny anonymous access
6. Set up lifecycle policies for cost optimization

### Mobile Configuration

1. **Orientation Lock** - Mobile devices are restricted to portrait orientation
2. **Admin Access** - Admin accounts must use desktop devices for full functionality
3. **Mobile Routes** - Certain features (Publications, Citations) are restricted on mobile
4. **Responsive Breakpoints** - Uses `sm:` (640px+) and `lg:` (1024px+) breakpoints
5. **Touch Optimization** - All interactive elements optimized for touch input

## Deployment

### Render.com Deployment

1. Connect your GitHub repository to Render
2. Use the provided `render.yaml` configuration
3. Set environment variables in Render dashboard
4. Deploy automatically on push to main branch

### Manual Deployment

1. **Build production assets**
   ```bash
   npm run build
   composer install --optimize-autoloader --no-dev
   ```

2. **Run migrations**
   ```bash
   php artisan migrate --force
   ```

3. **Cache configuration**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Usage

### For Users
1. **Privacy Agreement** - Accept the data privacy agreement before accessing the system
2. **Login** - Use your USeP Google account to access the system
3. **Submit Applications** - Create publication or citation incentive requests
4. **Upload Documents** - Attach required files and supporting documents
5. **Track Status** - Monitor your application progress in the dashboard
6. **Manage Signatures** - Upload and manage your digital signatures
7. **Mobile Access** - Full functionality available on mobile devices (admin accounts require desktop)

### For Signatories
1. **Review Requests** - Access pending documents requiring signatures
2. **Sign Documents** - Apply your digital signature to official documents
3. **Revert Signatures** - Undo signatures within the 24-hour window if needed

### For Administrators
1. **Desktop Access Required** - Admin accounts must be accessed from desktop devices
2. **Dashboard Overview** - Monitor system activity and statistics
3. **User Management** - Create and manage user accounts
4. **Request Processing** - Review, approve, or reject applications
5. **System Configuration** - Adjust application settings and parameters
6. **File Management** - Access and manage uploaded documents
7. **Privacy Compliance** - Monitor and manage privacy agreement acceptance

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

### Debug Commands
```bash
# Debug user roles
php artisan debug:user-roles

# Clean up temporary files
php artisan cleanup:temp-files
```

## Project Structure

```
pubcite/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Enums/               # Application enums
│   ├── Http/Controllers/    # HTTP controllers
│   ├── Mail/                # Email notifications
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── View/Components/     # Blade components
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── views/               # Blade templates
├── routes/                  # Route definitions
├── storage/                 # File storage
└── tests/                   # Test suites
```

## Recent Updates

### Mobile Responsiveness (Latest)
- **Complete Mobile Optimization** - Full responsive design for all pages and components
- **Mobile Dashboard** - Streamlined single-page mobile interface with hidden sidebar
- **Orientation Lock** - Portrait-only mobile experience for optimal usability
- **Touch-Friendly Interface** - Optimized buttons, dropdowns, and interactions
- **Mobile Security** - Device-specific access controls and URL injection protection

### Privacy & Security Enhancements
- **Data Privacy Agreement** - Mandatory privacy acceptance before system access
- **Session Synchronization** - Client-server state management for privacy compliance
- **Admin Mobile Restrictions** - Admin accounts restricted to desktop for full functionality
- **URL Injection Protection** - Server-side middleware preventing unauthorized mobile access

### UI/UX Improvements
- **Glassmorphism Design** - Modern backdrop blur effects throughout the interface
- **Progressive Enhancement** - Mobile-first approach with desktop enhancements
- **Responsive Typography** - Optimized text sizing for all device types
- **Mobile-First CSS** - Clean breakpoint strategy preserving desktop functionality

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
- Check the documentation in the `/docs` folder

## About USeP

The University of Southeastern Philippines (USeP) is a premier state university in the Philippines, committed to excellence in education, research, and community service. The Publication Unit supports faculty and researchers in their academic publishing endeavors.

---

**Developed for the University of Southeastern Philippines - Obrero** - @vete