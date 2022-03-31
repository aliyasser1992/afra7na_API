<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class flash_ads extends Model
{
    protected $fillable = [
            'title_ar',
            'title_en',
            'flag',
            'view',
            'turn',
            'link',
            'image',
            'country_id'
        ];

    public function Country()
    {
        return $this->belongsTo(country::class, 'country_id');
    }
}
