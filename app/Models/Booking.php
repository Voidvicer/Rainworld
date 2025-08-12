<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','room_id','check_in','check_out','guests','status','total_amount','payment_status','confirmation_code'];

    protected static function booted(){
        static::creating(function($m){ $m->confirmation_code = strtoupper(Str::random(10)); });
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function room(){ return $this->belongsTo(Room::class); }
}
