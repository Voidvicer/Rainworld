# Setup Instructions for Rainworld Picnic Island Resort Management System

## Quick Setup for Development

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- Git

### 1. Clone the Repository
```bash
git clone https://github.com/Voidvicer/Theme-Park-Web-App-with-Laravel.git
cd Theme-Park-Web-App-with-Laravel
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
```bash
# The SQLite database file is already included
# Run migrations to set up the database structure
php artisan migrate

# Seed the database with sample data using the improved seeder
php artisan db:seed

# OR load optimized sample data with the quick command
php artisan db:quick-sample --force
```

### 5. Build Assets
```bash
# Build frontend assets for production
npm run build

# OR for development with hot reload
npm run dev
```

### 6. Storage Setup
```bash
# Create storage symlink for file uploads
php artisan storage:link
```

### 7. Run the Application
```bash
php artisan serve
```

Visit `http://127.0.0.1:8000` in your browser.

## Default Test Accounts

After seeding, you can login with these test accounts:

- **Admin**: `admin@picnic.test` / `password`
- **Hotel Manager**: `manager@picnic.test` / `password`  
- **Ferry Staff**: `ferry@picnic.test` / `password`
- **Visitor**: `visitor@picnic.test` / `password`

## System Features

### Hotel Management
- Browse and book hotel rooms across 6 different resort properties
- Real-time availability checking and pricing
- Manage reservations with status tracking
- Apply promotional discounts and special offers
- Comprehensive booking reports and analytics

### Ferry Transportation Services  
- Book ferry tickets between Male City and Rainworld Picnic Island
- Digital boarding pass system with QR codes
- Ferry staff can issue and validate boarding passes
- Passenger manifest generation and management
- Real-time trip capacity and scheduling management

### Administrative Features
- Role-based access control (Admin, Hotel Staff, Ferry Staff)
- Comprehensive management dashboards for all services
- Revenue reporting and analytics across all operations
- User management and permission assignment
- System-wide configuration and settings

## Troubleshooting

### Missing Build Files
If you get asset loading errors:
```bash
npm run build
```

### Database Issues
If you encounter database problems, reset everything:
```bash
# Fresh migration with improved sample data
php artisan migrate:fresh --seed

# OR use the quick sample data command
php artisan db:quick-sample --force
```

### Permission Issues
On Linux/Mac systems, set proper permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear Application Cache
If you experience unexpected behavior:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## Production Deployment

For production deployment:

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Configure proper database credentials (MySQL/PostgreSQL recommended)
3. Run `php artisan config:cache` to optimize configuration
4. Set up proper web server (Apache/Nginx) configuration  
5. Configure SSL certificates for secure communication
6. Set up proper file permissions and ownership
7. Configure automated backup strategies
8. Set up monitoring and logging systems

## Available Console Commands

### Database Management
```bash
# Load optimized sample data quickly
php artisan db:quick-sample

# Reset and reload all data
php artisan app:reset-database

# Show test account information
php artisan app:show-test-accounts
```

### System Maintenance
```bash
# Put application in maintenance mode
php artisan down

# Bring application back online  
php artisan up

# View all available routes
php artisan route:list

# Process background jobs (if using queues)
php artisan queue:work
```

### Development Utilities
```bash
# Interactive shell for testing
php artisan tinker

# Run automated tests
php artisan test

# Generate IDE helper files (if using laravel-ide-helper)
php artisan ide-helper:generate
```
