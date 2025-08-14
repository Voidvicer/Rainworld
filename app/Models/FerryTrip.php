<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FerryTrip extends Model
{
    use HasFactory;
    protected $fillable = ['date','trip_type','depart_time','origin','destination','capacity','price','blocked','status','arrival_time'];

    public function tickets(){ return $this->hasMany(FerryTicket::class); }

    public function remainingSeats(): int {
        $sold = $this->tickets()->where('status', 'paid')->sum('quantity');
        return max(0, $this->capacity - $sold);
    }
    
    // Accessor for backward compatibility
    public function getPassengerCapacityAttribute()
    {
        return $this->capacity;
    }
    
    // Accessor for backward compatibility
    public function getDepartureDateAttribute()
    {
        return $this->date;
    }
    
    // Accessor for backward compatibility  
    public function getDepartureTimeAttribute()
    {
        return $this->depart_time;
    }
    
    // Accessor for backward compatibility
    public function getDepartureLocationAttribute()
    {
        return $this->origin;
    }
    
    // Accessor for backward compatibility
    public function getArrivalLocationAttribute()
    {
        return $this->destination;
    }
}
