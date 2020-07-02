<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Enums\DBConstant;

class Contract extends Model
{
    protected $primaryKey = 'contract_id';

    protected $fillable = [
        'plan_id',
        'business_card_id',
        'settlement_status',
        'start_date',
        'end_date',
        'price',
        'cancellation_reservation_flag'
    ];

    public function settlements()
    {
       return $this->hasMany(\App\Models\Settlement::class, 'contract_id', 'contract_id');
    }

    public function businessCard()
    {
        return $this->belongsTo(\App\Models\BusinessCard::class, 'business_card_id', 'business_card_id');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id', 'plan_id');
    }

    public function scopeCurrentContract($query)
    {
        $now = Carbon::now();

        return $query->where('start_date', '<=', $now)->where('end_date', '>=', $now)
                    ->whereIn('settlement_status', [DBConstant::SETTLEMENT_STATUS_APPROVED, DBConstant::SETTLEMENT_STATUS_CAPTURED]);
    }
}
