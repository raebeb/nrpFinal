<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    public $timestamps = false;

    public function user(){
      return $this->hasOne(User::class);
    }

    public function country(){
      return $this->belongsTo(Country::class);
    }
}
