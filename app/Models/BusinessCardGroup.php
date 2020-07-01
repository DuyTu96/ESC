<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCardGroup extends Model
{
    protected $primaryKey = [
        'business_card_id_grouping',
        'business_card_id_grouped'
    ];

    public $incrementing = false;
    
    protected $fillable = [
        'business_card_id_grouping',
        'business_card_id_grouped',
        'display_order'
    ];

    public function businessCard()
    {
        return $this->belongsTo(BusinessCard::class, 'business_card_id_grouped');
    }
}
