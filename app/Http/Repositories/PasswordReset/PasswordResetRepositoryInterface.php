<?php

declare(strict_types=1);

namespace App\Repositories\PasswordReset;

interface PasswordResetRepositoryInterface
{
    public function getTokenData($token);
}
