<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class main_category extends Model
{
    protected $fillable=['title_ar','title_en','image'];
    protected $hidden=['created_at','updated_at'];
}
