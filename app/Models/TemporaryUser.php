<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryUser extends Model
{
    protected $primaryKey = 'temporary_user_id';

    protected $fillable = [
        'session_id',
        'is_data_migrated'
    ];
}
