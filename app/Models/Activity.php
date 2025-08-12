<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = ['name','type','description','base_price','location_id','active'];

    public function schedules(){ return $this->hasMany(ActivitySchedule::class); }
}
