<?php

declare(strict_types=1);

namespace App\Repositories\PasswordReset;

use App\Models\PasswordReset;

use Carbon\Carbon;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    /**
     * Get token data.
     * @param $token
     * @return PasswordReset
     */
    public function getTokenData($token)
    {
        $expiredTime = Carbon::now()->addMinutes(10)->format('Y-m-d H:i:s');
        $tokenData = PasswordReset::where('token', $token)
            ->where('created_at', '<=', $expiredTime)
            ->whereColumn('created_at', 'updated_at')
            ->first();

        return $tokenData;
    }
}
