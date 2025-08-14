# Test Accounts for Stormshade Application

This file contains all the test accounts created by the database seeder for development and testing purposes.

## Default Password
**All accounts use the same password: `password`**

## Test Accounts

### 1. Admin Account
- **Email:** `admin@picnic.test`
- **Password:** `password`
- **Name:** Admin
- **Role:** `admin`
- **Access:** Full system access, all management pages

### 2. Hotel Manager Account
- **Email:** `manager@picnic.test`
- **Password:** `password`
- **Name:** Hotel Manager
- **Role:** `hotel_manager`
- **Access:** Hotel Management page, booking management

### 3. Ferry Staff Account
- **Email:** `ferry@picnic.test`
- **Password:** `password`
- **Name:** Ferry Staff
- **Role:** `ferry_staff`
- **Access:** Ferry Operations page, trip management

### 4. Visitor Account
- **Email:** `visitor@picnic.test`
- **Password:** `password`
- **Name:** Demo Visitor
- **Role:** `visitor`
- **Access:** Basic user access, booking and purchasing only

## Role Permissions

### Admin (`admin`)
- Access to all management interfaces
- Can access: Hotel Mgmt, Ferry Ops, Admin panel

### Hotel Manager (`hotel_manager`)
- Hotel and room management
- Booking oversight
- Can access: Hotel Mgmt

### Ferry Staff (`ferry_staff`)
- Ferry trip management
- Schedule management
- Can access: Ferry Ops

### Visitor (`visitor`)
- Standard user permissions
- Booking and purchasing only
- No management access

## Debug Information

To check role assignments and debug authentication issues:
1. Login with any account above
2. Navigate to `/debug-roles` in your browser
3. This will show your current user info and assigned roles

## Usage Notes

- These accounts are created automatically when running `php artisan db:seed`
- All accounts are for development/testing only
- The seeder checks for existing accounts before creating duplicates
- Roles are managed using the Spatie Permission package
