<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptHistory extends Model
{
    protected $primaryKey = 'receipt_history_id';

    protected $fillable = [
        'receiving_user_id',
        'received_at'
    ];

    public function details()
    {
        return $this->hasMany(ReceiptHistoryDetail::class, 'receipt_history_id');
    }
}
