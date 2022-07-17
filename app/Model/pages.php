<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class pages extends Model
{
    protected $fillable=['title_ar','title_en','description_ar','description_en'];
    protected $hidden=['created_at','updated_at'];
}
