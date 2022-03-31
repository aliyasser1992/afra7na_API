<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class special_event extends Model
{
    protected $hidden=['created_at','updated_at'];

    protected $fillable=['start_date','end_date','event_id'];

}
