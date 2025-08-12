<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FerryTicket extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','ferry_trip_id','quantity','status','total_amount','code','qr_path'];

    protected static function booted(){
        static::creating(function($m){ $m->code = strtoupper(Str::random(8)); });
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function trip(){ return $this->belongsTo(FerryTrip::class,'ferry_trip_id'); }
}
