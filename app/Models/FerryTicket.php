<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FerryTicket extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','ferry_trip_id','quantity','status','total_amount','code','qr_path'];

    protected static function booted(){
        static::creating(function($m){ 
            $m->code = strtoupper(Str::random(8));
            if (empty($m->status)) {
                $m->status = 'confirmed';
            }
        });
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function trip(){ return $this->belongsTo(FerryTrip::class,'ferry_trip_id'); }

    // Check if ticket should be expired
    public function checkExpiry(){
        if ($this->status === 'confirmed' && $this->trip && Carbon::parse($this->trip->date)->isPast()) {
            $this->update(['status' => 'expired']);
        }
        return $this;
    }

    // Automatically check expiry when accessing status
    public function getStatusAttribute($value){
        if ($value === 'confirmed' && $this->trip && Carbon::parse($this->trip->date)->isPast()) {
            $this->update(['status' => 'expired']);
            return 'expired';
        }
        return $value;
    }
}
