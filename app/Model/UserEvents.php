<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserEvents extends Model
{
    //
    protected  $fillable = ['user_id', 'country_id', 'region_id', 'wedding', 'occasions', 'invitations'];
    protected  $primaryKey = 'id';
    protected  $table ='user_events';
}
