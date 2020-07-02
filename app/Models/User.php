<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\SoftDeletes\CustomSoftDeletes;
use App\Enums\DBConstant;

class User extends Authenticatable implements JWTSubject
{
    use CustomSoftDeletes;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'email',
        'password',
        'remember_token',
        'qr_i_token',
        'qr_g_token',
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

    public function businessCards() {
        return $this->hasMany(BusinessCard::class, 'user_id')->where('business_cards.is_expired', DBConstant::BUSINESS_CARD_NOT_EXPIRED_FLAG);
    }
}
