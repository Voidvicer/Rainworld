# Theme Park Web App with Laravel

A comprehensive theme park management system built with Laravel 12, featuring hotel bookings, ferry tickets, theme park tickets, and activity reservations.

## 🌟 Features

### 🏨 Hotel Management
- Browse available hotels and rooms
- Make hotel reservations with date selection
- View booking history and manage existing bookings
- Room availability checking

### 🚢 Ferry Services
- Ferry trip scheduling and booking
- Real-time seat availability tracking
- Dynamic pricing and time slot selection
- QR code ticket generation
- Ticket cancellation with seat liberation

### 🎡 Theme Park Tickets
- Day pass and multi-day ticket options
- QR code generation for entry validation
- Ticket management and viewing

### 🎯 Activities & Events
- Activity browsing and booking
- Schedule management
- Capacity tracking and availability

### 👨‍💼 Admin Dashboard
- Comprehensive booking management
- Revenue reporting and analytics
- Ticket validation system
- User role management

## 🛠️ Tech Stack

- **Backend**: Laravel 12, PHP 8.2
- **Database**: SQLite (easily configurable for MySQL/PostgreSQL)
- **Frontend**: Blade templates with Tailwind CSS
- **Authentication**: Laravel Breeze
- **QR Codes**: QR code generation for tickets
- **Roles & Permissions**: Spatie Laravel Permission

## 📁 Project Structure

This is a standard Laravel 12 application with the following key directories:

```
Theme-Park-Web-App-with-Laravel/
├── app/                 # Application logic (Controllers, Models, etc.)
│   ├── Http/Controllers/
│   ├── Models/
│   └── ...
├── database/           # Database migrations and seeders
│   ├── migrations/
│   └── seeders/
├── resources/views/    # Blade templates
├── public/            # Web server document root
├── routes/            # Application routes
└── ...               # Standard Laravel directories
```

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Voidvicer/Theme-Park-Web-App-with-Laravel.git
   cd Theme-Park-Web-App-with-Laravel
   ```

2. **Install dependencies and setup**
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```

3. **Start the development server**
   ```bash
   php artisan serve
   ```

4. **Access the application**
   - Main site: http://localhost:8000
   - Admin login: admin@admin.com / password

## 📊 Database Schema

### Core Tables
- **users**: User authentication and profiles
- **hotels & rooms**: Hotel inventory management
- **bookings**: Hotel reservation system
- **ferry_trips & ferry_tickets**: Ferry transportation
- **theme_park_tickets**: Park entry tickets
- **activities & activity_bookings**: Event management

## 🎫 Key Features Implemented

### Dynamic Seat Management
- Real-time ferry seat tracking
- Automatic seat liberation on cancellation
- Color-coded availability indicators

### Professional UI/UX
- Responsive design with Tailwind CSS
- Dark/light mode compatibility
- Professional gradient styling
- Consistent button designs

### QR Code Integration
- Unique QR codes for all ticket types
- Easy validation system
- SVG format for scalability

### Role-Based Access
- Admin dashboard for management
- User-specific booking views
- Permission-based feature access

## 🔧 Configuration

### Environment Variables
Key configurations in `.env`:
- Database settings
- Application URL
- Mail configuration (for notifications)

### Database Setup
The application uses SQLite by default for easy setup, but can be configured for MySQL or PostgreSQL.

## 🧪 Sample Data

The application includes comprehensive seeders with:
- Sample hotels and rooms
- Ferry trip schedules
- Theme park ticket types
- User accounts (admin and regular users)
- Sample bookings and activities

## 📝 API Endpoints

### Public Routes
- `/` - Homepage
- `/hotels` - Hotel listings
- `/ferry/trips` - Ferry schedules
- `/park/tickets` - Theme park tickets
- `/activities` - Available activities

### Authenticated Routes
- `/bookings` - User booking history
- `/ferry/tickets` - Ferry ticket management
- `/dashboard` - User dashboard

### Admin Routes
- `/admin` - Admin dashboard
- `/manage/*` - Various management interfaces

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🆘 Support

For support and questions:
- Create an issue in this repository
- Contact: [Your contact information]

## 🎯 Recent Updates

- ✅ Fixed ferry seat counting system
- ✅ Implemented dynamic trip rendering
- ✅ Resolved Park Tickets page display issues
- ✅ Enhanced button styling consistency
- ✅ Fixed database constraint errors
- ✅ Improved QR code generation system

---

**Built with ❤️ using Laravel 12 and modern web technologies**
