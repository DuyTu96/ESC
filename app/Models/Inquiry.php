<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $primaryKey = 'inquiry_id';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'inquiry_type',
        'body'
    ];
}
