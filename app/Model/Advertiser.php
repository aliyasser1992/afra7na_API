<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model
{
    protected $fillable = [
        'category_id',
        'region_id',
        'name',
        'about',
        'all_categories',
        'all_regions',
        'email',
        'phone',
        'website',
        'img_url'
    ];

    protected $appends = [
        'hasGlobalCategory',
        'hasGlobalRegion'
    ];

    protected $casts = [
        'all_categories' => 'boolean',
        'all_regions' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function region()
    {
        return $this->belongsTo(region::class, 'region_id');
    }

    public function getHasGlobalCategoryAttribute()
    {
        return $this->category === null && $this->all_categories === true;
    }

    public function getHasGlobalRegionAttribute()
    {
        return $this->region === null && $this->all_regions === true;
    }

    public function banners()
    {
        return $this->hasMany(Banner::class);
    }

}
