<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use App\Models\FerryTrip;
use App\Models\FerryTicket;
use App\Models\Location;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
                    ]); App\Models\Booking;
use App\Models\FerryTrip;
use App\Models\FerryTicket;
use App\Models\User;
use App\Models\Location;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First create the existing basic data
        $this->createBasicData();
        
        // Create additional room types for existing hotels
        $this->createAdditionalRoomTypes();
        
        // Generate 2 months of sample data
        $this->generateSampleUsers();
        $this->generateSampleFerryData();
        $this->generateSampleBookingData();
        $this->generateSamplePromotions();
    }

    private function createBasicData()
    {
        // Hotel 1
        $hotel = Hotel::create([
            'name'=>'Lagoon View Resort','description'=>'Beachside hotel with family rooms',
            'address'=>'Picnic Island','contact'=>'+960 1234567','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel->id,'name'=>'Deluxe Family Room','type'=>'family','capacity'=>4,'total_rooms'=>10,'price_per_night'=>120.00,'amenities'=>['AC','WiFi','Breakfast']]);

        // Hotel 2
        $hotel2 = Hotel::create([
            'name'=>'Coral Garden Inn','description'=>'Boutique stay surrounded by coral gardens',
            'address'=>'Coral Bay','contact'=>'+960 5550101','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel2->id,'name'=>'Ocean View Suite','type'=>'suite','capacity'=>2,'total_rooms'=>5,'price_per_night'=>200.00,'amenities'=>['AC','WiFi','Sea View']]);

        // Hotel 3
        $hotel3 = Hotel::create([
            'name'=>'Sunrise Overwater Retreat','description'=>'Luxurious overwater bungalows with sunrise views',
            'address'=>'East Lagoon','contact'=>'+960 5550202','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel3->id,'name'=>'Garden Villa','type'=>'villa','capacity'=>3,'total_rooms'=>6,'price_per_night'=>180.00,'amenities'=>['AC','WiFi','Private Patio']]);

        // Hotel 4
        $hotel4 = Hotel::create([
            'name'=>'Azure Reef Lodge','description'=>'Modern eco-friendly lodge with stunning reef views',
            'address'=>'North Shore','contact'=>'+960 5550303','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel4->id,'name'=>'Honeymoon Suite','type'=>'suite','capacity'=>2,'total_rooms'=>3,'price_per_night'=>260.00,'amenities'=>['AC','WiFi','Jacuzzi','Sea View']]);

        // Hotel 5
        $hotel5 = Hotel::create([
            'name'=>'Paradise Cove Villas','description'=>'Exclusive private villas with direct beach access',
            'address'=>'South Beach','contact'=>'+960 5550404','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel5->id,'name'=>'Overwater Bungalow','type'=>'bungalow','capacity'=>2,'total_rooms'=>8,'price_per_night'=>320.00,'amenities'=>['AC','WiFi','Glass Floor','Breakfast']]);

        // Hotel 6
        $hotel6 = Hotel::create([
            'name'=>'Moonlight Bay Resort','description'=>'Romantic beachfront resort perfect for couples',
            'address'=>'West Coast','contact'=>'+960 5550505','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel6->id,'name'=>'Signature Panorama Suite','type'=>'suite','capacity'=>2,'total_rooms'=>2,'price_per_night'=>450.00,'amenities'=>['AC','WiFi','Panoramic Deck','Butler']]);

        Location::create(['name'=>'Main Jetty','lat'=>4.175278,'lng'=>73.508889,'description'=>'Arrival point','category'=>'transport','active'=>true]);
        Location::create(['name'=>'Beach A','lat'=>4.1762,'lng'=>73.5095,'description'=>'Family beach','category'=>'beach','active'=>true]);

        Promotion::create(['title'=>'Ferry Holiday Special','content'=>'15% off ferry bookings','starts_at'=>now()->subDay(),'ends_at'=>now()->addDays(7),'active'=>true,'scope'=>'ferry']);
    }

    private function createAdditionalRoomTypes()
    {
        $hotels = Hotel::all();
        
        $uniqueRoomTypes = [
            'Presidential Suite',
            'Executive Penthouse', 
            'Royal Oceanview',
            'Ambassador Suite',
            'Premier Villa',
            'Elite Harbor View',
            'Grand Terrace Suite',
            'Luxury Garden Villa'
        ];

        foreach ($hotels as $index => $hotel) {
            // Create 1 unique room type per hotel
            $roomTypeName = $uniqueRoomTypes[$index % count($uniqueRoomTypes)];
            
            // Create premium room type with higher price
            $basePrice = rand(350, 500);
            
            for ($i = 1; $i <= 3; $i++) {
                Room::create([
                    'hotel_id' => $hotel->id,
                    'name' => $roomTypeName . ' ' . $i,
                    'type' => $roomTypeName,
                    'price_per_night' => $basePrice + (rand(-50, 100)),
                    'capacity' => rand(2, 4),
                    'total_rooms' => 1,
                    'amenities' => ['AC', 'WiFi', 'Premium View', 'Butler Service']
                ]);
            }
        }
    }

    private function generateSampleUsers()
    {
        $firstNames = ['James', 'Mary', 'Robert', 'Patricia', 'Michael', 'Jennifer', 'William', 'Linda', 'David', 'Elizabeth', 'Richard', 'Barbara', 'Joseph', 'Susan', 'Thomas', 'Jessica', 'Christopher', 'Sarah', 'Daniel', 'Karen'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];
        
        for ($i = 0; $i < 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            
            User::create([
                'name' => $firstName . ' ' . $lastName,
                'email' => strtolower($firstName . '.' . $lastName . rand(1, 999) . '@example.com'),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }

    private function generateSampleFerryData()
    {
        // Only Male City ↔ Rainworld routes
        $routes = [
            ['Male City', 'Rainworld'],
            ['Rainworld', 'Male City']
        ];

        // Specific schedule for Male City ↔ Rainworld
        $schedule = [
            'Male City' => ['07:00', '08:00', '09:00', '10:00'], // Departures to Rainworld
            'Rainworld' => ['14:00', '16:00', '18:00', '20:00']  // Returns to Male City
        ];
        
        // Generate trips for past 2 months and next month
        for ($date = Carbon::now()->subMonths(2); $date <= Carbon::now()->addMonth(); $date->addDay()) {
            foreach ($routes as $route) {
                $times = $schedule[$route[0]]; // Get specific times for each origin
                
                foreach ($times as $time) {
                    $trip = FerryTrip::create([
                        'origin' => $route[0],
                        'destination' => $route[1],
                        'date' => $date->format('Y-m-d'),
                        'depart_time' => $time,
                        'capacity' => rand(50, 100),
                        'price' => 15 // Consistent $15 pricing
                    ]);

                    // Generate tickets for this trip (30-90% occupancy)
                    $occupancyRate = rand(30, 90) / 100;
                    $ticketsToCreate = floor($trip->capacity * $occupancyRate);
                    
                    for ($t = 0; $t < $ticketsToCreate; $t++) {
                        $user = User::inRandomOrder()->first();
                        if (!$user) continue;
                        
                        $quantity = rand(1, 3);
                        
                        $ticket = FerryTicket::create([
                            'user_id' => $user->id,
                            'ferry_trip_id' => $trip->id,
                            'quantity' => $quantity,
                            'status' => 'paid',
                            'total_amount' => $trip->price * $quantity,
                        ]);

                        // 70% chance of having boarding pass issued for past trips
                        if ($date < Carbon::now() && rand(1, 100) <= 70) {
                            $ticket->update([
                                'pass_issued_at' => $date->copy()->addHours(rand(1, 12))
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function generateSampleBookingData()
    {
        $users = User::all();
        $hotels = Hotel::all();
        
        // Generate bookings for past 2 months and next month
        for ($date = Carbon::now()->subMonths(2); $date <= Carbon::now()->addMonth(); $date->addDays(rand(1, 3))) {
            foreach ($hotels as $hotel) {
                // 60-90% occupancy rate
                $rooms = $hotel->rooms;
                $occupancyRate = rand(60, 90) / 100;
                $roomsToBook = floor($rooms->count() * $occupancyRate);
                
                if ($roomsToBook > 0) {
                    $selectedRooms = $rooms->random(min($roomsToBook, $rooms->count()));
                    
                    foreach ($selectedRooms as $room) {
                        $user = $users->random();
                        $checkIn = $date->copy();
                        $nights = rand(1, 7);
                        $checkOut = $checkIn->copy()->addDays($nights);
                        
                        $totalAmount = $room->price_per_night * $nights;
                        
                        // 5% chance of cancellation for future bookings
                        $status = 'confirmed';
                        if ($checkIn > Carbon::now() && rand(1, 100) <= 5) {
                            $status = 'canceled';
                        } elseif ($checkOut < Carbon::now()) {
                            $status = 'completed';
                        }
                        
                        Booking::create([
                            'user_id' => $user->id,
                            'room_id' => $room->id,
                            'check_in' => $checkIn->format('Y-m-d'),
                            'check_out' => $checkOut->format('Y-m-d'),
                            'guests' => rand(1, $room->capacity),
                            'total_amount' => $totalAmount,
                            'status' => $status,
                        ]);
                    }
                }
            }
        }
    }

    private function generateSamplePromotions()
    {
        $promoTypes = [
            ['Early Bird Special', 'Book 30 days in advance and save!', 15],
            ['Weekend Getaway', 'Perfect for weekend trips', 20],
            ['Extended Stay', 'Stay longer, pay less', 25],
            ['Family Package', 'Great deals for families', 18],
            ['Seasonal Special', 'Limited time offer', 30],
            ['Ferry + Hotel Combo', 'Combined booking discount', 22]
        ];

        foreach ($promoTypes as $promo) {
            Promotion::create([
                'title' => $promo[0],
                'content' => $promo[1],
                'discount_percentage' => $promo[2],
                'starts_at' => Carbon::now()->subMonth(),
                'ends_at' => Carbon::now()->addMonths(2),
                'active' => rand(1, 100) <= 80, // 80% active
                'scope' => rand(1, 100) <= 50 ? 'ferry' : 'hotel'
            ]);
        }
    }
}
