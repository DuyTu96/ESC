<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Enums\DBConstant;
use App\Traits\SoftDeletes\CustomSoftDeletes;

class BusinessCard extends Model
{
    use CustomSoftDeletes;

    protected $primaryKey = 'business_card_id';

    protected $fillable = [
        'company_id',
        'user_id',
        'str_subscription_id',
        'str_subscription_sched_id',
        'setting_code',
        'last_name_kanji',
        'first_name_kanji',
        'name_kanji_for_index',
        'last_name_kana',
        'first_name_kana',
        'name_kana_for_index',
        'department_id',
        'position_id',
        'employee_number',
        'hire_date',
        'email',
        'phone',
        'display_order',
        'is_expired',
        'is_archived'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function contracts()
    {
        return $this->hasMany(\App\Models\Contract::class, 'business_card_id', 'business_card_id');
    }
    
    public function addtionalBusinessCardProfiles()
    {
        return $this->hasMany(AdditionalBusinessCardProfile::class, 'business_card_id');
    }

    public function grouping() 
    {
        return $this->hasMany(\App\Models\BusinessCardGroup::class, 'business_card_id_grouping', 'business_card_id');
    }

    public function grouped()
    {
        return $this->hasMany(\App\Models\BusinessCardGroup::class, 'business_card_id_grouped', 'business_card_id');
    }

    public function receiveds()
    {
        return $this->hasMany(ReceivedBusinessCard::class, 'business_card_id');
    }

    public function temporaryReceiveds()
    {
        return $this->hasMany(TemporarilyReceivedBusinessCard::class, 'business_card_id');
    }
}
