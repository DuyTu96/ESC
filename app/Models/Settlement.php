<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $primaryKey = 'settlement_id';

    protected $fillable = [
        'str_payment_id',
        'contract_id',
        'currency',
        'status',
        'approval_error_reason',
        'approval_failed_at',
        'approved_amount',
        'approved_at',
        'capture_error_reason',
        'capture_failed_at',
        'captured_amount',
        'captured_at',
        'cancellation_error_reason',
        'cancellation_failed_at',
        'canceled_amount',
        'canceled_at'
    ];

    public function contract()
    {
        return $this->belongsTo(\App\Models\Contract::class, 'contract_id');
    }
}
