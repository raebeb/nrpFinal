<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
	public $timestamps = false;
	
    public function schedule(){
    	return $this->hasOne(Schedule::class);
    }
}
