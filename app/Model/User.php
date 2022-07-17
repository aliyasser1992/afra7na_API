<?php

namespace App\Model;

use App\Filters\QueryFilter;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    protected $fillable = [
        'name', 'phone', 'password', 'country_id', 'region_id', 'area_id', 'verification_code', 'state'
    ];

    public function country()
    {
        return $this->belongsTo('App\Model\country');
    }

    public function region()
    {
        return $this->belongsTo('App\Model\region');
    }

    public function area()
    {
        return $this->belongsTo('App\Model\area');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    protected $primaryKey = 'id';

    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at',
        'verification_code', 'state'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


}
