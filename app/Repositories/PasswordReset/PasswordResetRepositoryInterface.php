<?php

declare(strict_types=1);

namespace App\Repositories\PasswordReset;

use App\Repositories\EloquentRepositoryInterface;

interface PasswordResetRepositoryInterface extends EloquentRepositoryInterface
{
    public function getPasswordResetByToken($token);

    public function getPasswordResetByEmail($email);
}
