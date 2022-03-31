<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class support extends Model
{
    protected $table="support";
    protected $fillable = [
        'name',
        'email',
        'message',
    ];
    protected $hidden = [
        'created_at','updated_at',
    ];
}
