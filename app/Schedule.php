<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public function user(){
      return $this->belongsTo(User::class);
    }

    public function file(){
      return $this->hasMany(File::class);
    }

    public function service(){
    	return $this->belongsTo(Service::class);
    }
}
