<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BannerReach extends Model
{
    protected $fillable = [
        'banner_id',
        'user_id'
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
