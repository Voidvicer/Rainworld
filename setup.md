# Setup Instructions for Island Resort Management System

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
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed

# OR load improved sample data
php artisan db:quick-sample --force
```

### 5. Build Assets
```bash
# Build frontend assets
npm run build

# OR for development with hot reload
npm run dev
```

### 6. Storage Setup
```bash
# Create storage symlink
php artisan storage:link
```

### 7. Run the Application
```bash
php artisan serve
```

Visit `http://127.0.0.1:8000` in your browser.

## Default Accounts

After seeding, you can login with these test accounts:

- **Admin**: `admin@picnic.test` / `password`
- **Hotel Manager**: `manager@picnic.test` / `password`  
- **Ferry Staff**: `ferry@picnic.test` / `password`
- **Visitor**: `visitor@picnic.test` / `password`

## Features Overview

### 🏨 Hotel Management
- Browse and book hotel rooms
- View availability and pricing
- Manage reservations
- Apply promotional discounts

### 🚢 Ferry Services  
- Book ferry tickets between locations
- Issue boarding passes for validated tickets
- Manage passenger lists and trip schedules
- QR code-based ticket system

### 👨‍💼 Staff Management
- Role-based access control
- Hotel and ferry management dashboards
- Reporting and analytics
- Passenger validation and pass issuance

## Troubleshooting

### Missing Build Files
If you get asset errors, run:
```bash
npm run build
```

### Database Issues
If you have database problems, reset it:
```bash
php artisan migrate:fresh --seed
```

### Permission Issues
On Linux/Mac, you may need to set permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear Cache
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
2. Configure proper database credentials
3. Run `php artisan config:cache` to cache configuration
4. Set up proper web server (Apache/Nginx) configuration
5. Configure SSL certificates
6. Set up proper file permissions
7. Configure backup strategies

## Additional Commands

### Sample Data Management
```bash
# Quick load sample data
php artisan db:quick-sample

# Load from SQL dump
php artisan db:load-dump

# Create new sample data dump
php create_database_dump.php
```

### Maintenance
```bash
# Put app in maintenance mode
php artisan down

# Bring app back online  
php artisan up

# Check system health
php artisan route:list
php artisan queue:work
```
