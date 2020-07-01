<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalCompanyProfile extends Model
{
    protected $fillable = [
        'company_id',
        'item_title',
        'item_body',
        'display_order'
    ];
}
