<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    protected $primaryKey = 'prefecture_id';

    protected $fillable = [
        'name',
        'display_order',
        'is_default'
    ];
}
