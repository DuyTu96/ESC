<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ManagementPortalUser extends Authenticatable implements JWTSubject
{
    protected $primaryKey = 'management_portal_user_id';

    protected $fillable = [
        'email',
        'password',
        'remember_token',
        'is_archived'
    ];

    protected $hidden = ['remember_token', 'password'];
    
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
}
