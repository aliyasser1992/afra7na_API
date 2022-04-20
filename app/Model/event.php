<?php

namespace App\Model;

use App\Filters\QueryFilter;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use willvincent\Rateable\Rateable;

class event extends Model
{
    use Rateable;
    use SoftDeletes;
    protected $appends = ['thump',/*'rating', 'userRate',*/ 'userFavourite'];
    protected $hidden = [
//        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'title',
        'description',
        'ad_image',
        'ad_image_thump',
        'ad_image_sort',
        'special_image',
        'ads_link',
        'video',
        'main_category_id',
        'country_id',
        'region_id',
        'area_id',
        'address',
        'phone',
        'user_id',
        'invitation_start_time',
        'invitation_end_time',
        'longitude',
        'latitude',
        'special',
        'from',
        'to',
        'created_at'
    ];

    public function getThumpAttribute($value){
        return $this->ad_image_thump;
    }
    public function getLongitudeAttribute($value)
    {
        if ($value == 0)
            return null;
    }

    public function getLatitudeAttribute($value)
    {
        if ($value == 0)
            return null;
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function country()
    {
        return $this->belongsTo('App\Model\country')->select('id', 'title_ar', 'title_en');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User')->select('id', 'name');
    }

    public function region()
    {
        return $this->belongsTo('App\Model\region')->select('id', 'title_ar', 'title_en');
    }

    public function area()
    {
        return $this->belongsTo('App\Model\area')->select('id', 'name');
    }

    public function media()
    {
        return $this->hasMany('App\Model\media');
    }

    public function special_event()
    {
        return $this->hasMany('App\Model\special_event');
    }

    public function category()
    {
        return $this->belongsTo('App\Model\main_category', 'main_category_id');
    }

    public function favourite()
    {
        return $this->morphMany('App\Model\favourite', 'favourite');
    }
/*
    public function getRatingAttribute()
    {
        return $this->attributes['rating'] = $this->averageRating() == null ? 0 : round($this->averageRating());
    }

    public function getUserRateAttribute()
    {
        return $this->attributes['userRate'] = round($this->userAverageRating);
    }
*/
    public function getUserFavouriteAttribute()
    {
        if (Auth::user() != null) {
            $check = $this->favourite()->where('user_id', Auth::id())->first();
            return $this->attributes['userFavourite'] = $check == null ? 0 : 1;
        } else {
            return $this->attributes['userFavourite'] = 0;
        }

    }
}
