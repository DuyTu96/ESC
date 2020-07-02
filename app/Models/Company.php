<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SoftDeletes\CustomSoftDeletes;

class Company extends Model
{
    use CustomSoftDeletes;

    protected $primaryKey = 'company_id';

    protected $fillable = [
        'str_customer_id',
        'name',
        'name_for_index',
        'name_en',
        'url',
        'phone',
        'postal_code',
        'prefecture_id',
        'city',
        'subsequent_address',
        'is_archived'
    ];

    public function companyAdminUsers()
    {
        return $this->hasMany(\App\Models\CompanyAdminUser::class, 'company_id', 'company_id');
    }

    public function prefecture()
    {
        return $this->belongsTo(\App\Models\Prefecture::class, 'prefecture_id');
    }

    public function additionalCompanyProfiles()
    {
        return $this->hasMany(\App\Models\AdditionalCompanyProfile::class, 'company_id');
    }

    public function businessCards()
    {
        return $this->hasMany(\App\Models\BusinessCard::class, 'company_id');
    }
}
