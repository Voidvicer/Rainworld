<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = ['hotel_id','name','type','capacity','total_rooms','price_per_night','amenities'];
    protected $casts = ['amenities'=>'array'];

    public function hotel(){ return $this->belongsTo(Hotel::class); }
    public function bookings(){ return $this->hasMany(Booking::class); }
}
