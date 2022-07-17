<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class region extends Model
{
    protected $hidden = [
        'created_at','updated_at',
    ];
    protected $fillable=['title_ar','title_en','country_id'];

    public function country()
    {
        return $this->belongsTo('App\Model\country');
    }
}
