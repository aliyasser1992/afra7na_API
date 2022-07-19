<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'advertiser_id',
        'event_id',
        'type',
        'link',
        'external_link',
        'content',
        'price',
        'position'
    ];

    public function event()
    {
        return $this->belongsTo(event::class);
    }

    public function advertiser()
    {
        return $this->belongsTo(Advertiser::class);
    }

    public function reaches()
    {
        return $this->hasMany(BannerReach::class);
    }
}
