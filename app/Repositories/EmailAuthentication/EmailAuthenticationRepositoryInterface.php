<?php

declare(strict_types=1);

namespace App\Repositories\EmailAuthentication;

use App\Repositories\EloquentRepositoryInterface;

interface EmailAuthenticationRepositoryInterface extends EloquentRepositoryInterface
{
    public function getEmailAuthByToken($token);
}
