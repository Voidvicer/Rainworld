# Stormshade Theme Park - Database Management

This project includes optimized database seeding functionality to speed up development.

## Quick Start with Sample Data

### Option 1: Quick Sample Data Loading (RECOMMENDED - ~20 seconds)
```bash
php artisan db:quick-sample
```

### Option 2: Manual Seeding (Slower - ~2-3 minutes)
```bash
php artisan db:seed --class=ImprovedSampleDataSeeder
```

### Option 3: Reset Everything and Start Fresh
```bash
php artisan migrate:fresh --seed
php artisan db:quick-sample --force
```

## What's Included in Sample Data

### Users (50 sample users)
- 50 realistic Maldivian names with email addresses
- All active accounts with password: `password123`

### Hotels & Rooms
- All existing hotels get additional premium room types
- Executive Ocean Suites, Presidential Villas, etc.
- Proper capacity limits enforced

### Ferry Operations
- **Ferry Trips**: July 10 - August 25, 2025
- **Trip Types**: Both departure and return trips
- **Routes**: 
  - Male City ↔ Picnic Island
  - Male City ↔ Airport  
  - Male City ↔ Vilimale
  - Picnic Island ↔ Airport
- **Capacity**: 200 passengers per trip
- **Times**: 6 departure times daily (07:00, 09:30, 12:00, 14:30, 17:00, 19:30)

### Hotel Bookings
- **Period**: July 18 - August 18, 2025
- **Total**: ~200 bookings
- **NO OVERBOOKING**: Proper availability checking implemented
- Spread across different hotels and room types

### Ferry Tickets  
- **Total**: ~400 tickets
- **Only for valid guests**: Tickets created only for users with valid hotel bookings
- Includes both arrival and departure tickets
- Proper capacity limits enforced

### Additional Features
- Locations and promotions
- Realistic booking patterns
- Proper date ranges leading up to current date (August 13, 2025)

## Recent Fixes Applied

### 1. Ferry Reports Route Fixed ✅
- Added missing `manage.ferry.export.revenue` route
- Added `exportRevenue()` method to FerryManagementController
- Ferry reports page now loads without errors

### 2. Hotel Overbooking Prevention ✅
- Added proper availability checking in seeder
- Validates room capacity before creating bookings
- No more bookings exceeding room limits

### 3. Ferry Trip Types ✅
- Added `trip_type` column: `departure` or `return`
- Ferry schedule now shows both departures and returns
- Users can book either departure, return, or both

### 4. Flexible Ferry Booking ✅
- **FIXED**: No longer requires both departure AND return
- Users can book just departure, just return, or both
- More realistic booking patterns

### 5. Extended Date Range ✅
- Sample data now covers July 18 - August 18, 2025
- Plenty of bookings and ferry trips up to current date
- More realistic for testing and demonstration

### 6. Fast Sample Data Loading ✅
- New `db:quick-sample` command loads data in ~20 seconds
- Optimized seeder with progress indicators
- Reliable and consistent results

## Performance Comparison

| Method | Time | Use Case |
|--------|------|----------|
| `db:quick-sample` | ~20 seconds | **Recommended for development** |
| `db:seed` | ~2-3 minutes | Manual seeding |
| `migrate:fresh --seed` | ~8-10 minutes | Complete reset |

The quick-sample command is **6-18x faster** than traditional seeding!

## Test Accounts

Use these accounts for testing different roles:
- **Admin**: admin@stormshade.com / admin123
- **Hotel Manager**: manager@picnic.test / manager123  
- **Ferry Staff**: ferry@picnic.test / ferry123
- **Regular Visitor**: visitor@picnic.test / visitor123

## Usage Tips

### For Development
```bash
# Quick refresh of sample data
php artisan db:quick-sample --force
```

### For Testing
```bash
# Reset everything cleanly
php artisan migrate:fresh --seed
php artisan db:quick-sample --force
```

### For Demos
```bash
# Just load fresh sample data
php artisan db:quick-sample
```

## Sample Data Statistics

After running `db:quick-sample`, you'll have:
- **54 Users** (4 staff + 50 guests)
- **200 Hotel Bookings** (no overbooking)
- **2,256 Ferry Trips** (both departures and returns)
- **400 Ferry Tickets** (linked to valid bookings)
- **Premium Rooms** added to all hotels

## Next Steps

1. ✅ Ferry reports page working
2. ✅ No hotel overbooking
3. ✅ Extended sample data through August 13th
4. ✅ Departure and return ferry trips
5. ✅ Optional departure/return booking
6. ✅ Fast database loading (~20 seconds)

**The application is now ready for efficient development and testing!**
