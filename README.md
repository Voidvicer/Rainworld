# Rainworld Picnic Island Resort Management System

A comprehensive Laravel-based management system for Rainworld Picnic Island Resort, featuring complete hotel booking and ferry transportation management with role-based access control.

## 🏨 System Overview

Rainworld Picnic Island is a full-featured resort management platform designed for island operations, integrating hotel bookings with ferry transportation services. The system provides dedicated interfaces for different staff roles while maintaining a unified guest experience.

## ✨ Core Features

### 🏨 Hotel Management
- **Hotel & Room Management**: Create and manage multiple hotels with detailed room configurations
- **Intelligent Booking System**: Real-time availability checking with conflict prevention
- **Dynamic Promotions**: Percentage-based discount system with flexible date ranges
- **Staff Dashboard**: Comprehensive booking oversight with status management
- **Revenue Reporting**: Detailed financial analytics and booking trend analysis

### ⛴️ Ferry Transportation
- **Trip Scheduling**: Advanced ferry schedule management with capacity controls
- **Ticket Booking System**: Individual and bulk ticket purchasing
- **Digital Pass System**: Secure boarding pass generation and validation
- **Passenger Manifests**: Real-time passenger lists with ticket code tracking
- **Revenue Analytics**: Ferry-specific financial reporting and capacity analysis
- **Staff Validation Tools**: Ticket verification and pass issuance systems

### 👥 User Management & Security
- **Role-Based Access Control**: Admin, Hotel Staff, Ferry Staff with granular permissions
- **Secure Authentication**: Laravel Sanctum-powered session management
- **User Registration**: Streamlined account creation with email verification
- **Profile Management**: User dashboard with booking history and account settings

### 🔧 Technical Features
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Real-Time Updates**: Live availability and booking status updates
- **Database Integrity**: SQLite with proper constraints and foreign keys
- **Error Handling**: Comprehensive logging and user-friendly error messages
- **Performance Optimized**: Efficient queries and caching strategies

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm
- SQLite

### Installation Steps

1. **Clone & Setup**
   ```bash
   git clone [repository-url]
   cd Rainworld-Picnic-Island
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate --seed
   ```

4. **Asset Compilation**
   ```bash
   npm run build
   ```

5. **Launch Application**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## 👤 Test Accounts

Refer to `TEST_ACCOUNTS.MD` for comprehensive login credentials for all user types.

## 🏗️ System Architecture

### Technology Stack
- **Backend**: Laravel 11 with PHP 8.2
- **Database**: SQLite with optimized schema design
- **Frontend**: Blade templates with Tailwind CSS
- **JavaScript**: Alpine.js for reactive components
- **Build Tool**: Vite for asset optimization
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission package

### Database Schema
- **Users & Roles**: User authentication with role-based permissions
- **Hotels & Rooms**: Multi-hotel support with room management
- **Bookings**: Hotel reservation system with status tracking
- **Ferry System**: Trips, tickets, and pass issuance
- **Locations & Promotions**: Geographic data and marketing tools

## 📋 Management Interfaces

### Admin Dashboard
- System-wide analytics and reporting
- User management and role assignment
- Global settings and configuration
- Cross-system data export capabilities

### Hotel Staff Interface
- Room availability and booking management
- Guest check-in/check-out procedures
- Promotion setup and management
- Hotel-specific reporting

### Ferry Staff Interface
- Trip scheduling and capacity management
- Ticket validation and pass issuance
- Passenger manifest generation
- Ferry-specific analytics

## 🔧 Development Tools

### Console Commands
- `php artisan app:reset-database` - Reset and reseed database
- `php artisan app:show-test-accounts` - Display test account information
- `php artisan route:list` - View all application routes
- `php artisan migrate:fresh --seed` - Fresh database migration

### Database Management
- SQLite browser for direct database access
- Comprehensive seeders for test data
- Migration files with proper foreign key constraints
- Factory classes for data generation

## 📚 Project Structure

```
app/
├── Http/Controllers/     # Request handling logic
│   ├── Admin/           # Administrative interfaces
│   ├── Auth/            # Authentication controllers
│   └── ...              # Core application controllers
├── Models/              # Eloquent data models
├── Middleware/          # Request middleware
└── Providers/           # Service providers

resources/
├── views/               # Blade templates
│   ├── manage/         # Staff management interfaces
│   ├── auth/           # Authentication views
│   └── components/     # Reusable components
├── css/                # Tailwind CSS styles
└── js/                 # Alpine.js components

database/
├── migrations/         # Database schema definitions
├── seeders/           # Data seeding classes
└── factories/         # Model factories
```

## 🤝 Contributing

This project follows Laravel coding standards and uses:
- PSR-4 autoloading
- Semantic versioning
- Conventional commit messages
- Comprehensive test coverage

## 📄 License

This project is proprietary software developed for Rainworld Picnic Island Resort.

## 🆘 Support

For technical support or feature requests, please refer to the `IMPLEMENTATION_SUMMARY.md` file for detailed system documentation and troubleshooting guides.
