<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseService
{
    public function __construct()
    {
    }

    /**
     * Write debug log.
     *
     * @param string      $message
     * @param null|string $code
     */
    protected function logDebug($message, $code = null): void
    {
        // Log context
        $context = $this->getContext($code);

        Log::debug($message, $context);
    }

    /**
     * Write info log.
     *
     * @param string      $message
     * @param null|string $code
     */
    protected function logInfo($message, $code = null): void
    {
        // Log context
        $context = $this->getContext($code);

        Log::info($message, $context);
    }

    /**
     * Write warning log.
     *
     * @param string      $message
     * @param null|string $code
     */
    protected function logWarning($message, $code = null): void
    {
        // Log context
        $context = $this->getContext($code);

        Log::warning($message, $context);
    }

    /**
     * Get pagination offset.
     *
     * @param int $page
     * @param int $limit
     * @return int
     */
    protected function getPaginationOffset(int $page, int $limit): int
    {
        return ($page - 1) * $limit;
    }

    /**
     * Get blocked user array.
     *
     * @param int $userId
     * @return array
     */
    protected function getBlockedUserIds(int $userId): array
    {
        // Get user_id array that the user is blocked from
        $fromUserIds = DB::table('blocks')
            ->where('to_user_id', $userId)
            ->pluck('from_user_id')->toArray();

        // Get user_id array that the user blocked
        $toUserIds = DB::table('blocks')
            ->where('from_user_id', $userId)
            ->pluck('to_user_id')->toArray();

        return array_merge($fromUserIds, $toUserIds);
    }

    /**
     * Get reported user array.
     *
     * @param int $userId
     * @return array
     */
    protected function getReportedUserIds(int $userId): array
    {
        // Get user_id array that the user is reported from
        $fromUserIds = DB::table('reports')
            ->where('to_user_id', $userId)
            ->pluck('from_user_id')->toArray();

        // Get user_id array that the user reported
        $toUserIds = DB::table('reports')
            ->where('from_user_id', $userId)
            ->pluck('to_user_id')->toArray();

        return array_merge($fromUserIds, $toUserIds);
    }

    /**
     * Get log context.
     *
     * @param null|string $code
     * @return array
     */
    private function getContext($code = null): array
    {
        if ($code) {
            return $context = [
                'code' => $code,
                'user_id' => Auth::check() ? Auth::user()->id : null,
                'input' => request()->all(),
            ];
        }

        return $context = [
                'user_id' => Auth::check() ? Auth::user()->id : null,
                'input' => request()->all(),
            ];
    }
}
