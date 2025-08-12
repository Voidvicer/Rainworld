<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ThemeParkTicket extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','visit_date','quantity','status','total_amount','code','qr_path'];

    protected static function booted(){
        static::creating(function($m){ 
            $m->code = strtoupper(Str::random(8));
            if (empty($m->status)) {
                $m->status = 'confirmed';
            }
        });
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function bookings(){ return $this->hasMany(ActivityBooking::class); }

    // Check if ticket should be expired
    public function checkExpiry(){
        if ($this->status === 'confirmed' && Carbon::parse($this->visit_date)->isPast()) {
            $this->update(['status' => 'expired']);
        }
        return $this;
    }

    // Automatically check expiry when accessing status
    public function getStatusAttribute($value){
        if ($value === 'confirmed' && Carbon::parse($this->visit_date)->isPast()) {
            $this->update(['status' => 'expired']);
            return 'expired';
        }
        return $value;
    }
}
