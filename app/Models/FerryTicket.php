<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FerryTicket extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','ferry_trip_id','quantity','status','total_amount','code','qr_path','qr_code','pass_issued_at'];

    protected $casts = [
        'pass_issued_at' => 'datetime',
    ];

    protected static function booted(){
        static::creating(function($m){ 
            $m->code = strtoupper(Str::random(8));
            if (empty($m->status)) {
                $m->status = 'paid'; // Use 'paid' instead of 'confirmed' as per database enum constraint
            }
        });
    }

    public function user(){ return $this->belongsTo(User::class); }
    public function trip(){ return $this->belongsTo(FerryTrip::class,'ferry_trip_id'); }

    // Check if ticket should be considered expired (for display purposes only)
    public function checkExpiry(){
        // This method is kept for compatibility but doesn't update the database
        // Use the display_status attribute to show expired status in the UI
        return $this;
    }

    // Get the raw status without any automatic updates
    public function getStatusAttribute($value){
        // Return the actual database value without attempting to update it
        return $value;
    }

    // Check if boarding pass has been issued
    public function getPassIssuedAttribute(){
        return !is_null($this->pass_issued_at);
    }

    // Get display status (shows what should be displayed in the UI)
    public function getDisplayStatusAttribute(){
        // If pass is issued, show as confirmed
        if ($this->pass_issued_at) {
            return 'confirmed';
        }
        
        // If trip date has passed and ticket was paid, show as expired
        if ($this->status === 'paid' && $this->trip && Carbon::parse($this->trip->date)->isPast()) {
            return 'expired';
        }
        
        // Otherwise show the actual status
        return $this->status;
    }
}
