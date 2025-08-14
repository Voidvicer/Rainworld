<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Booking;
use App\Models\FerryTrip;
use App\Models\FerryTicket;
use App\Models\Location;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ImprovedSampleDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🏗️  Creating improved sample data...');
        
        // Clear existing data (except admin users and test accounts)
        $this->command->info('🗑️  Clearing existing sample data...');
        $preserveEmails = ['admin@stormshade.com', 'admin@picnic.test', 'manager@picnic.test', 'ferry@picnic.test', 'visitor@picnic.test'];
        FerryTicket::whereHas('user', function($q) use ($preserveEmails) { $q->whereNotIn('email', $preserveEmails); })->delete();
        Booking::whereHas('user', function($q) use ($preserveEmails) { $q->whereNotIn('email', $preserveEmails); })->delete();
        FerryTrip::query()->delete();
        
        // Clear hotels and rooms to prevent duplicates
        \DB::table('rooms')->delete();
        \DB::table('hotels')->delete();
        
        User::whereNotIn('email', $preserveEmails)->delete();
        
        // 1. Create sample users
        $this->command->info('👥 Creating sample users...');
        $users = $this->createSampleUsers();
        
        // 2. Create locations if they don't exist
        $this->command->info('📍 Creating locations...');
        $this->createLocations();
        
        // 3. Create 6 hotels with 2 room types each
        $this->command->info('🏨 Creating 6 hotels with room types...');
        $this->createHotelsAndRooms();

        // 4. Create ferry trips with proper capacity (200)
        $this->command->info('⛴️  Creating ferry trips...');
        $this->createFerryTrips();
        
        // 5. Create hotel bookings (spread over July 18 - August 18)
        $this->command->info('🛏️  Creating hotel bookings...');
        $this->createHotelBookings($users);
        
        // 6. Create ferry tickets (only for users with valid hotel bookings)
        $this->command->info('🎫 Creating ferry tickets...');
        $this->createFerryTickets($users);
        
        // 7. Create promotions
        $this->command->info('🎁 Creating promotions...');
        $this->createPromotions();
        
        $this->command->info('✅ Improved sample data created successfully!');
    }
    
    private function createSampleUsers()
    {
        $users = [];
        $names = [
            'Ahmed Hassan', 'Fatima Ali', 'Mohamed Ibrahim', 'Aisha Rashid',
            'Omar Abdullah', 'Mariam Nasir', 'Ali Waheed', 'Zara Moosa',
            'Ibrahim Manik', 'Aminath Shaheeda', 'Hassan Zareer', 'Khadeeja Naseem',
            'Adam Saleem', 'Hawwa Shiuna', 'Ismail Rasheed', 'Fathmath Nisha',
            'Mohamed Amir', 'Rugiyya Ahmed', 'Abdullah Hameed', 'Aishath Waheeda',
            'Hussain Waheed', 'Mariyam Shifa', 'Ahmed Niyaz', 'Aminath Riza',
            'Hassan Naeem', 'Fathimath Dhiye', 'Ibrahim Shifaz', 'Hawwa Zahira',
            'Mohamed Nasheed', 'Aishath Velezinee', 'Ahmed Shareef', 'Mariyam Nazra',
            'Abdullah Maumoon', 'Aminath Jameel', 'Ismail Nazeer', 'Fathmath Goma',
            'Hassan Latheef', 'Hawwa Lubna', 'Mohamed Waheed', 'Aishath Shiham',
            'Ahmed Nazim', 'Mariyam Shakeela', 'Ibrahim Solih', 'Aminath Shujau',
            'Hussain Amr', 'Fathimath Afiya', 'Mohamed Shihab', 'Hawwa Zeeniya',
            'Abdullah Shahid', 'Aishath Azima'
        ];
        
        for ($i = 0; $i < 50; $i++) {
            $name = $names[$i] ?? "Guest User " . ($i + 1);
            $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
            
            $users[] = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'active' => true,
                'created_at' => Carbon::now()->subDays(rand(1, 60)),
            ]);
        }
        
        return collect($users);
    }
    
    private function createLocations()
    {
        $locations = [
            ['name' => 'Male City', 'category' => 'city', 'description' => 'Capital city of Maldives', 'lat' => 4.1755, 'lng' => 73.5093],
            ['name' => 'Rainworld', 'category' => 'resort', 'description' => 'Premier tropical resort destination', 'lat' => 4.2000, 'lng' => 73.5500],
            ['name' => 'Picnic Island', 'category' => 'resort', 'description' => 'Popular resort destination', 'lat' => 4.1800, 'lng' => 73.5200],
            ['name' => 'Airport', 'category' => 'airport', 'description' => 'Velana International Airport', 'lat' => 4.1919, 'lng' => 73.5289],
            ['name' => 'Hulhule', 'category' => 'island', 'description' => 'Airport island', 'lat' => 4.1900, 'lng' => 73.5280],
            ['name' => 'Vilimale', 'category' => 'residential', 'description' => 'Residential area', 'lat' => 4.1800, 'lng' => 73.5100],
        ];
        
        foreach ($locations as $location) {
            Location::firstOrCreate(
                ['name' => $location['name']],
                array_merge($location, ['active' => true])
            );
        }
    }
    
    private function createHotelsAndRooms()
    {
        $hotelsData = [
            [
                'name' => 'Rainworld Resort & Spa',
                'description' => 'Luxury beachfront resort with world-class amenities and stunning ocean views',
                'address' => 'North Malé Atoll, Maldives',
                'contact' => '+960 664-2233',
                'active' => true,
                'rooms' => [
                    [
                        'name' => 'Ocean Villa',
                        'type' => 'villa',
                        'capacity' => 2,
                        'price_per_night' => 450.00,
                        'total_rooms' => 8,
                        'amenities' => ['Private Pool', 'Ocean View', 'Butler Service', 'WiFi', 'AC']
                    ],
                    [
                        'name' => 'Beach Bungalow',
                        'type' => 'bungalow', 
                        'capacity' => 4,
                        'price_per_night' => 320.00,
                        'total_rooms' => 12,
                        'amenities' => ['Beach Access', 'Terrace', 'WiFi', 'AC', 'Mini Bar']
                    ]
                ]
            ],
            [
                'name' => 'Sunset Paradise Hotel',
                'description' => 'Modern hotel with panoramic sunset views and contemporary amenities',
                'address' => 'Hulhumalé, Maldives', 
                'contact' => '+960 330-5544',
                'active' => true,
                'rooms' => [
                    [
                        'name' => 'Sunset Suite',
                        'type' => 'suite',
                        'capacity' => 2,
                        'price_per_night' => 280.00,
                        'total_rooms' => 6,
                        'amenities' => ['Sunset View', 'Balcony', 'WiFi', 'AC', 'Kitchenette']
                    ],
                    [
                        'name' => 'Deluxe Room',
                        'type' => 'deluxe',
                        'capacity' => 3,
                        'price_per_night' => 180.00,
                        'total_rooms' => 15,
                        'amenities' => ['City View', 'WiFi', 'AC', 'Breakfast', 'Workspace']
                    ]
                ]
            ],
            [
                'name' => 'Crystal Lagoon Resort',
                'description' => 'Eco-friendly resort surrounded by crystal clear lagoons and tropical gardens',
                'address' => 'Ari Atoll, Maldives',
                'contact' => '+960 668-7799',
                'active' => true,
                'rooms' => [
                    [
                        'name' => 'Lagoon Villa',
                        'type' => 'villa',
                        'capacity' => 2,
                        'price_per_night' => 380.00,
                        'total_rooms' => 10,
                        'amenities' => ['Lagoon View', 'Private Deck', 'Eco-Friendly', 'WiFi', 'AC']
                    ],
                    [
                        'name' => 'Garden Room',
                        'type' => 'standard',
                        'capacity' => 2,
                        'price_per_night' => 220.00,
                        'total_rooms' => 18,
                        'amenities' => ['Garden View', 'Patio', 'WiFi', 'AC', 'Mini Fridge']
                    ]
                ]
            ],
            [
                'name' => 'Azure Bay Resort',
                'description' => 'Boutique resort offering intimate luxury with personalized service',
                'address' => 'Baa Atoll, Maldives',
                'contact' => '+960 672-1188',
                'active' => true,
                'rooms' => [
                    [
                        'name' => 'Azure Suite',
                        'type' => 'suite',
                        'capacity' => 2,
                        'price_per_night' => 350.00,
                        'total_rooms' => 4,
                        'amenities' => ['Bay View', 'Jacuzzi', 'Private Terrace', 'WiFi', 'AC']
                    ],
                    [
                        'name' => 'Comfort Twin',
                        'type' => 'twin',
                        'capacity' => 2,
                        'price_per_night' => 160.00,
                        'total_rooms' => 20,
                        'amenities' => ['Twin Beds', 'Shared Balcony', 'WiFi', 'AC', 'Breakfast']
                    ]
                ]
            ],
            [
                'name' => 'Palm Grove Hotel',
                'description' => 'Family-friendly hotel with extensive facilities and entertainment options',
                'address' => 'Malé City, Maldives',
                'contact' => '+960 330-2255',
                'active' => true,
                'rooms' => [
                    [
                        'name' => 'Family Suite',
                        'type' => 'family',
                        'capacity' => 6,
                        'price_per_night' => 240.00,
                        'total_rooms' => 8,
                        'amenities' => ['Family Size', 'Kids Area', 'WiFi', 'AC', 'Connecting Rooms']
                    ],
                    [
                        'name' => 'Standard Room',
                        'type' => 'standard',
                        'capacity' => 2,
                        'price_per_night' => 120.00,
                        'total_rooms' => 25,
                        'amenities' => ['City View', 'WiFi', 'AC', 'Daily Housekeeping', 'TV']
                    ]
                ]
            ],
            [
                'name' => 'Emerald Waters Resort',
                'description' => 'Adults-only resort featuring overwater villas and pristine coral reefs',
                'address' => 'Lhaviyani Atoll, Maldives',
                'contact' => '+960 665-9988',
                'active' => true,
                'rooms' => [
                    [
                        'name' => 'Overwater Villa',
                        'type' => 'overwater',
                        'capacity' => 2,
                        'price_per_night' => 520.00,
                        'total_rooms' => 6,
                        'amenities' => ['Over Water', 'Glass Floor', 'Direct Ocean Access', 'WiFi', 'AC']
                    ],
                    [
                        'name' => 'Beach Villa',
                        'type' => 'villa',
                        'capacity' => 2,
                        'price_per_night' => 400.00,
                        'total_rooms' => 10,
                        'amenities' => ['Beach Front', 'Private Beach', 'Outdoor Shower', 'WiFi', 'AC']
                    ]
                ]
            ]
        ];

        foreach ($hotelsData as $hotelData) {
            $hotel = Hotel::create([
                'name' => $hotelData['name'],
                'description' => $hotelData['description'],
                'address' => $hotelData['address'],
                'contact' => $hotelData['contact'],
                'active' => $hotelData['active']
            ]);

            foreach ($hotelData['rooms'] as $roomData) {
                Room::create([
                    'hotel_id' => $hotel->id,
                    'name' => $roomData['name'],
                    'type' => $roomData['type'],
                    'capacity' => $roomData['capacity'],
                    'price_per_night' => $roomData['price_per_night'],
                    'total_rooms' => $roomData['total_rooms'],
                    'amenities' => $roomData['amenities']
                ]);
            }
        }
    }
    
    private function createFerryTrips()
    {
        // Only Male City ↔ Rainworld routes allowed
        $routes = [
            ['Male City', 'Rainworld', 'departure'],
            ['Rainworld', 'Male City', 'return'],
        ];
        
        // Specific schedule for Male City ↔ Rainworld
        $schedule = [
            'departure' => ['07:00', '08:00', '09:00', '10:00'], // Male City → Rainworld
            'return' => ['14:00', '16:00', '18:00', '20:00']     // Rainworld → Male City
        ];
        
        // Create trips from July 10 to August 25 (extended range)
        $startDate = Carbon::create(2025, 7, 10);
        $endDate = Carbon::create(2025, 8, 25);
        
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            foreach ($routes as $route) {
                $times = $schedule[$route[2]];
                
                foreach ($times as $time) {
                    FerryTrip::create([
                        'origin' => $route[0],
                        'destination' => $route[1],
                        'date' => $currentDate->format('Y-m-d'),
                        'depart_time' => $time,
                        'capacity' => 200, // Set capacity to 200 as requested
                        'price' => 15, // Consistent $15 pricing
                        'blocked' => false,
                        'trip_type' => $route[2], // departure or return
                    ]);
                }
            }
            $currentDate->addDay();
        }
    }
    
    private function createHotelBookings($users)
    {
        // Get all users if empty parameter passed
        if (!$users || $users->isEmpty()) {
            $users = User::all();
        }
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found to create bookings for.');
            return;
        }
        
        $hotels = Hotel::with('rooms')->get();
        
        // Create bookings spread from July 18 to August 18
        $startDate = Carbon::create(2025, 7, 18);
        $endDate = Carbon::create(2025, 8, 18);
        
        $bookingsCreated = 0;
        
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate && $bookingsCreated < 200) {
            
            foreach ($hotels as $hotel) {
                
                foreach ($hotel->rooms as $room) {
                    
                    // Check availability for this room on this date range
                    $checkIn = $currentDate->copy()->addDays(rand(0, 3));
                    $stayDuration = rand(2, 7);
                    $checkOut = $checkIn->copy()->addDays($stayDuration);
                    
                    // Ensure checkout doesn't exceed our date range
                    if ($checkOut > $endDate) {
                        $checkOut = $endDate->copy();
                        $stayDuration = $checkIn->diffInDays($checkOut);
                    }
                    
                    if ($stayDuration >= 2) {
                        // Check if this room is available (prevent overbooking)
                        $existingBookings = Booking::where('room_id', $room->id)
                            ->where('status', '!=', 'canceled')
                            ->where(function($q) use ($checkIn, $checkOut) {
                                $q->whereBetween('check_in', [$checkIn->format('Y-m-d'), $checkOut->format('Y-m-d')])
                                  ->orWhereBetween('check_out', [$checkIn->format('Y-m-d'), $checkOut->format('Y-m-d')])
                                  ->orWhere(function($qq) use ($checkIn, $checkOut) { 
                                      $qq->where('check_in', '<=', $checkIn->format('Y-m-d'))
                                         ->where('check_out', '>=', $checkOut->format('Y-m-d')); 
                                  });
                            })->count();
                        
                        // Only book if room is available
                        if ($existingBookings < $room->total_rooms && rand(1, 100) <= 40) { // 40% chance to book
                            $user = $users->random();
                            $totalAmount = $room->price_per_night * $stayDuration;
                            
                            Booking::create([
                                'user_id' => $user->id,
                                'room_id' => $room->id,
                                'check_in' => $checkIn->format('Y-m-d'),
                                'check_out' => $checkOut->format('Y-m-d'),
                                'total_amount' => $totalAmount,
                                'guests' => rand(1, $room->capacity),
                                'status' => collect(['confirmed', 'pending'])->random(),
                                'payment_status' => 'paid',
                                'created_at' => $checkIn->copy()->subDays(rand(1, 14)),
                            ]);
                            
                            $bookingsCreated++;
                            
                            if ($bookingsCreated >= 200) break;
                        }
                    }
                    
                    if ($bookingsCreated >= 200) break;
                }
                
                if ($bookingsCreated >= 200) break;
            }
            
            $currentDate->addDay();
        }
        
        $this->command->info("Created {$bookingsCreated} hotel bookings (no overbooking)");
    }
    
    private function createFerryTickets($users)
    {
        // Only create ferry tickets for users who have valid hotel bookings
        $validBookings = Booking::with(['user', 'room.hotel'])
            ->where('status', '!=', 'canceled')
            ->where('payment_status', 'paid')
            ->get();
        
        $ticketsCreated = 0;
        
        foreach ($validBookings as $booking) {
            $user = $booking->user;
            $checkIn = Carbon::parse($booking->check_in);
            $checkOut = Carbon::parse($booking->check_out);
            
            // Create arrival ticket (within 3 days before check-in)
            $arrivalDate = $checkIn->copy()->subDays(rand(0, 3));
            $arrivalTrips = FerryTrip::whereDate('date', $arrivalDate->format('Y-m-d'))
                ->where('destination', '!=', 'Male City') // Going to resort/island
                ->get();
            
            if ($arrivalTrips->isNotEmpty()) {
                $arrivalTrip = $arrivalTrips->random();
                
                // Check current booking count for this trip
                $currentBookings = FerryTicket::where('ferry_trip_id', $arrivalTrip->id)
                    ->where('status', 'paid')
                    ->sum('quantity');
                
                $availableCapacity = $arrivalTrip->capacity - $currentBookings;
                
                if ($availableCapacity > 0) {
                    $quantity = min(rand(1, 4), $availableCapacity);
                    
                    FerryTicket::create([
                        'user_id' => $user->id,
                        'ferry_trip_id' => $arrivalTrip->id,
                        'quantity' => $quantity,
                        'total_amount' => $arrivalTrip->price * $quantity,
                        'status' => 'paid',
                        'code' => 'FT' . strtoupper(substr(md5(uniqid()), 0, 8)),
                        'created_at' => $arrivalDate->copy()->subDays(rand(1, 10)),
                    ]);
                    
                    $ticketsCreated++;
                }
            }
            
            // Create departure ticket (on check-out day or 1-2 days after)
            $departureDate = $checkOut->copy()->addDays(rand(0, 2));
            $departureTrips = FerryTrip::whereDate('date', $departureDate->format('Y-m-d'))
                ->where('destination', 'Male City') // Going back to Male
                ->get();
            
            if ($departureTrips->isNotEmpty()) {
                $departureTrip = $departureTrips->random();
                
                // Check current booking count for this trip
                $currentBookings = FerryTicket::where('ferry_trip_id', $departureTrip->id)
                    ->where('status', 'paid')
                    ->sum('quantity');
                
                $availableCapacity = $departureTrip->capacity - $currentBookings;
                
                if ($availableCapacity > 0) {
                    $quantity = min(rand(1, 4), $availableCapacity);
                    
                    FerryTicket::create([
                        'user_id' => $user->id,
                        'ferry_trip_id' => $departureTrip->id,
                        'quantity' => $quantity,
                        'total_amount' => $departureTrip->price * $quantity,
                        'status' => 'paid',
                        'code' => 'FT' . strtoupper(substr(md5(uniqid()), 0, 8)),
                        'created_at' => $departureDate->copy()->subDays(rand(1, 5)),
                    ]);
                    
                    $ticketsCreated++;
                }
            }
        }
        
        $this->command->info("Created {$ticketsCreated} ferry tickets (only for users with valid hotel bookings)");
    }
    
    private function createPromotions()
    {
        $promotions = [
            [
                'title' => 'Summer Paradise Package',
                'content' => 'Special summer rates with complimentary sunset cruise',
                'starts_at' => '2025-07-01 00:00:00',
                'ends_at' => '2025-08-31 23:59:59',
                'active' => true,
            ],
            [
                'title' => 'Early Bird Special',
                'content' => 'Book 30 days in advance and save',
                'starts_at' => '2025-07-15 00:00:00',
                'ends_at' => '2025-09-15 23:59:59',
                'active' => true,
            ],
            [
                'title' => 'Extended Stay Discount',
                'content' => 'Stay 5 nights or more and get special rates',
                'starts_at' => '2025-07-20 00:00:00',
                'ends_at' => '2025-08-20 23:59:59',
                'active' => true,
            ],
        ];
        
        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}
