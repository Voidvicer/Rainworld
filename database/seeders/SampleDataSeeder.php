<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\FerryTrip;
use App\Models\Activity;
use App\Models\ActivitySchedule;
use App\Models\Location;
use App\Models\Promotion;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::create([
            'name'=>'Lagoon View Resort','description'=>'Beachside hotel with family rooms',
            'address'=>'Picnic Island','contact'=>'+960 1234567','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel->id,'name'=>'Deluxe Family Room','type'=>'family','capacity'=>4,'total_rooms'=>10,'price_per_night'=>120.00,'amenities'=>['AC','WiFi','Breakfast']]);
        Room::create(['hotel_id'=>$hotel->id,'name'=>'Ocean View Suite','type'=>'suite','capacity'=>2,'total_rooms'=>5,'price_per_night'=>200.00,'amenities'=>['AC','WiFi','Sea View']]);

        $hotel2 = Hotel::create([
            'name'=>'Coral Garden Inn','description'=>'Boutique stay surrounded by coral gardens',
            'address'=>'Coral Bay','contact'=>'+960 5550101','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel2->id,'name'=>'Garden Villa','type'=>'villa','capacity'=>3,'total_rooms'=>6,'price_per_night'=>180.00,'amenities'=>['AC','WiFi','Private Patio']]);
        Room::create(['hotel_id'=>$hotel2->id,'name'=>'Honeymoon Suite','type'=>'suite','capacity'=>2,'total_rooms'=>3,'price_per_night'=>260.00,'amenities'=>['AC','WiFi','Jacuzzi','Sea View']]);

        $hotel3 = Hotel::create([
            'name'=>'Sunrise Overwater Retreat','description'=>'Luxurious overwater bungalows with sunrise views',
            'address'=>'East Lagoon','contact'=>'+960 5550202','active'=>true
        ]);
        Room::create(['hotel_id'=>$hotel3->id,'name'=>'Overwater Bungalow','type'=>'bungalow','capacity'=>2,'total_rooms'=>8,'price_per_night'=>320.00,'amenities'=>['AC','WiFi','Glass Floor','Breakfast']]);
        Room::create(['hotel_id'=>$hotel3->id,'name'=>'Signature Panorama Suite','type'=>'suite','capacity'=>2,'total_rooms'=>2,'price_per_night'=>450.00,'amenities'=>['AC','WiFi','Panoramic Deck','Butler']]);

        // Create multiple ferry departure times per day for next 5 days
        $times = ['08:00','09:30','11:00','13:30'];
        for ($i=0;$i<5;$i++){
            foreach($times as $t){
                FerryTrip::create([
                    'date'=> now()->addDays($i)->toDateString(),'depart_time'=>$t,
                    'origin'=>"Male' City",'destination'=>'Picnic Island','capacity'=>50,'price'=>15.00,
                    'blocked'=>false
                ]);
            }
        }

        $ride = Activity::create(['name'=>'Cyclone Rollercoaster','type'=>'ride','description'=>'High-speed thrills','base_price'=>10,'active'=>true]);
        $show = Activity::create(['name'=>'Lagoon Light Show','type'=>'show','description'=>'Evening spectacle','base_price'=>8,'active'=>true]);
        $beach = Activity::create(['name'=>'Sunset Beach Volleyball','type'=>'beach','description'=>'Team fun at the beach','base_price'=>5,'active'=>true]);

        foreach ([$ride,$show,$beach] as $act) {
            for ($d=0; $d<3; $d++) {
                ActivitySchedule::create(['activity_id'=>$act->id,'date'=> now()->addDays($d+1)->toDateString(),'start_time'=>'15:00','end_time'=>'16:00','capacity'=>30]);
            }
        }

        Location::create(['name'=>'Main Jetty','lat'=>4.175278,'lng'=>73.508889,'description'=>'Arrival point','category'=>'transport','active'=>true]);
        Location::create(['name'=>'Theme Park Gate','lat'=>4.1759,'lng'=>73.5092,'description'=>'Park entrance','category'=>'park','active'=>true]);
        Location::create(['name'=>'Beach A','lat'=>4.1762,'lng'=>73.5095,'description'=>'Family beach','category'=>'beach','active'=>true]);

        Promotion::create(['title'=>'Opening Week Discount','content'=>'20% off all park tickets','starts_at'=>now()->subDay(),'ends_at'=>now()->addDays(7),'active'=>true,'scope'=>'park']);
    }
}
