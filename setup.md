# Setup Instructions for Rainworld Picnic Island Resort Management System

## Quick Setup for Development

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- Git

### 1. If downloaded from zip file:
```bash
# You can directly run server as everything should be set.
pho artisan serve 
# Please be patient as it may take 30 seconds to load for the first time.
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

### Database Management
```bash
# Load optimized sample data quickly
php artisan db:quick-sample

# Reset and reload all data
php artisan app:reset-database

# Show test account information
php artisan app:show-test-accounts
```
