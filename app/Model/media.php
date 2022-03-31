<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Image;

class media extends Model
{
    protected $fillable = [
        'event_id',
        'ads_id',
        'image',
        'thump'
    ];
    protected $hidden = [
        'created_at', 'updated_at',
    ];

//     public function getThumpAttribute($value)
//     {
//         return 'http://afr7na.com/' . $value;
//     }
}
