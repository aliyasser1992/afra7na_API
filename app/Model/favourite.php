<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class favourite extends Model
{
    protected $table="favourites";
    protected $fillable = [
        'favourite_id',
        'favourite_type',
        'user_id'
    ];
    public function favourite()
    {
        return $this->morphTo();
    }

}
