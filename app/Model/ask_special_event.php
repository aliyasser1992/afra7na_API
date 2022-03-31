<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ask_special_event extends Model
{
    protected $fillable = [
        'event_id',
        'start_time'
    ];

    public function event()
    {
        return $this->belongsTo('App\Model\event')
        ->select('id','title','special_image','user_id','country_id','main_category_id')->with('user','category','country');
    }
}
