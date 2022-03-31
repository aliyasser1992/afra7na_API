<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class country extends Model
{
    protected $hidden = [
        'created_at','updated_at',
    ];

    protected $fillable=['title_ar','title_en','currency_ar','currency_en','code','image'];

}
