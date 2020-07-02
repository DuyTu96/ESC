<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalBusinessCardProfile extends Model
{
    protected $fillable = [
        'business_card_id',
        'item_title',
        'item_body',
        'display_order'
    ];
}
