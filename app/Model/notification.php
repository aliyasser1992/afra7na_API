<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'event_id',
        'user_id',
    ];
    protected $hidden = [
        'created_at','updated_at',
    ];
}
