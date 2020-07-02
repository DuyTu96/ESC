<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\SoftDeletes\CustomSoftDeletes;

class CompanyAdminUser extends Authenticatable implements JWTSubject
{
    use CustomSoftDeletes;

    protected $primaryKey = 'company_admin_user_id';

    protected $fillable = [
        'company_id',
        'email',
        'password',
        'remember_token',
        'is_authenticated',
        'is_archived'
    ];

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

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }
}
