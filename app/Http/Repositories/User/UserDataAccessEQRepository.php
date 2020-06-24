<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\User;

class UserDataAccessEQRepository implements UserDataAccessRepositoryInterface
{
    public function getAll()
    {
        return User::all();
    }
}
