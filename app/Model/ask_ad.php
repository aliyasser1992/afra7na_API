<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ask_ad extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'brief'
    ];
}
