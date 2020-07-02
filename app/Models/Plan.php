<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $primaryKey = 'plan_id';

    protected $fillable = [
        'name',
        'price',
        'contract_period'
    ];

    public function contracts()
    {
        return $this->hasMany(\App\Models\Contract::class, 'plan_id', 'plan_id');
    }
}
