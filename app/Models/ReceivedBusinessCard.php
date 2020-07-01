<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivedBusinessCard extends Model
{   
    protected $primaryKey = [
        'user_id',
        'business_card_id'
    ];

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'business_card_id',
        'received_at'
    ];
}
