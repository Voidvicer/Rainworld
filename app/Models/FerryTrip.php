<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FerryTrip extends Model
{
    use HasFactory;
    protected $fillable = ['date','depart_time','origin','destination','capacity','price','blocked'];

    public function tickets(){ return $this->hasMany(FerryTicket::class); }

    public function remainingSeats(): int {
        $sold = $this->tickets()->where('status', 'paid')->sum('quantity');
        return max(0, $this->capacity - $sold);
    }
}
