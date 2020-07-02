<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagePath extends Model
{
    protected $fillable = [
        'company_id',
        'business_card_id',
        'file_name',
        'dir_path',
        'image_url',
        'display_order'
    ];
}
