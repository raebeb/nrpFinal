<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    protected $visible = [
        'schedule_id','name_receiver','lastname_receiver','storage_path'
    ];

    public $timestamps = false;

    public function schedule(){
      return $this->belongsTo(Schedule::class);
    }
}
