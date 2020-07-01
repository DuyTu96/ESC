<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryReceiptHistory extends Model
{
    protected $primaryKey = 'temporary_receipt_history_id';

    protected $fillable = [
        'temporary_user_id',
        'received_at'
    ];

    public function details()
    {
        return $this->hasMany(TemporaryReceiptHistoryDetail::class, 'temporary_receipt_history_id');
    }
}
