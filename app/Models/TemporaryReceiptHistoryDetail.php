<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryReceiptHistoryDetail extends Model
{
    protected $fillable = [
        'temporary_receipt_history_id',
        'business_card_id'
    ];

    public function businessCard()
    {
        return $this->belongsTo(BusinessCard::class, 'business_card_id');
    }
}
