<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    protected $fillable = ['title','content','starts_at','ends_at','active','image_url','scope','promotable_id','promotable_type','discount_percentage'];
    protected $casts = ['starts_at'=>'datetime','ends_at'=>'datetime','active'=>'boolean'];
    public function promotable(){ return $this->morphTo(); }
}
