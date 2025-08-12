<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityBooking extends Model
{
    use HasFactory;
    protected $fillable = ['theme_park_ticket_id','activity_schedule_id','quantity','status','total_amount'];

    public function ticket(){ return $this->belongsTo(ThemeParkTicket::class,'theme_park_ticket_id'); }
    public function schedule(){ return $this->belongsTo(ActivitySchedule::class,'activity_schedule_id'); }
}
