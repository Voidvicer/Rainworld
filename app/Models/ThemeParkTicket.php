<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ThemeParkTicket extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','visit_date','quantity','status','total_amount','code','qr_path'];

    protected static function booted(){
        static::creating(function($m){ $m->code = strtoupper(Str::random(8)); });
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function bookings(){ return $this->hasMany(ActivityBooking::class); }
}
