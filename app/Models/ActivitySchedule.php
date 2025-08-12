<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitySchedule extends Model
{
    use HasFactory;
    protected $fillable = ['activity_id','date','start_time','end_time','capacity'];

    public function activity(){ return $this->belongsTo(Activity::class); }
    public function bookings(){ return $this->hasMany(ActivityBooking::class); }
    public function remaining(): int { return max(0, $this->capacity - $this->bookings()->sum('quantity')); }
}
