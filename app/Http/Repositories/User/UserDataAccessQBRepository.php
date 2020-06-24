<?php

declare(strict_types=1);

namespace App\Repositories\User;

use DB;

class UserDataAccessQBRepository implements UserDataAccessRepositoryInterface
{
    protected $table = 'users';

    public function getAll()
    {
        return DB::table($this->table)->get();
    }
}
