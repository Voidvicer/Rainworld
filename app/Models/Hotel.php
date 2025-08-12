<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','address','contact','active'];

    public function rooms(){ return $this->hasMany(Room::class); }
    public function promotions(){ return $this->morphMany(Promotion::class, 'promotable'); }
}
