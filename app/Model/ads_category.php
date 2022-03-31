<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ads_category extends Model
{
    protected $table="ads_category";

    protected $fillable=['title_ar','title_en','image'];
    protected $hidden=['created_at','updated_at'];
     public function ads()
    {
        return $this->hasMany('App\Model\ads');
    }
}
