<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ads extends Model
{
    protected $table = "ads";

    protected $fillable = [
        'flag',
        'title',
        'brief',
        'image',
        'ads_category_id',
        'link',
        'phone',
        'user_id',
        'instagram_url',
        'twitter_url',
        'snap_chat_url',
        'whatsapp_number',
        'website_url',
        'views',
        'pin',
        'is_admin',
        'country_id',
        'special',
        'from',
        'to'
    ];

    protected $hidden = ['created_at', 'updated_at'];


    public function ads_category()
    {
        return $this->belongsTo('App\Model\ads_category');
    }

    public function media()
    {
        return $this->hasMany('App\Model\media');
    }

    public function adsImages()
    {
        return $this->hasMany(AdsImages::class, 'ads_id');
    }

    public function country()
    {
        return $this->belongsTo(country::class, 'country_id');
    }
}
