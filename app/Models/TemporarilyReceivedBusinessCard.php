<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporarilyReceivedBusinessCard extends Model
{   
    protected $primaryKey = [
        'temporary_user_id', 
        'business_card_id'
    ];

    public $incrementing = false;

    protected $fillable = [
        'temporary_user_id',
        'business_card_id',
        'received_at'
    ];
}
