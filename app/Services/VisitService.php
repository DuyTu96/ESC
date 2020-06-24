<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Flag;
use App\Enums\PerPageLimit;
use App\Exceptions\DBException;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VisitService extends BaseService
{
    public function __construct()
    {
    }

    /**
     * Get visits per page.
     *
     * @param int $userId
     * @param int $page
     * @return Collection
     */
    public function getVisitsPerPage(int $userId, int $page): Collection
    {
        try {
            // Get pagination limit and offset
            $limit = PerPageLimit::GET_VISITS;
            $offset = $page != 0 && $this->getPaginationOffset($page, $limit);

            // Get visit entities and pluck out from_user_id array
            if ($page != 0) {
                $fromUserIds = DB::table('visits')
                    ->where('to_user_id', $userId)
                    ->offset($offset)
                    ->limit($limit)
                    ->pluck('from_user_id')->toArray();
            } else {
                $fromUserIds = DB::table('visits')
                    ->where('to_user_id', $userId)
                    ->where('is_read', Flag::FALSE)
                    ->pluck('from_user_id')->toArray();
            }

            // Get blocked user_id array and reported user_id array
            $blockedUserIds = $this->getBlockedUserIds($userId);
            $reportedUserIds = $this->getReportedUserIds($userId);

            // Select relations and create query
            $query = User::active()->with([
                'profile_images',
                'visited_countries',
                'visiting_countries',
                'tags',
            ]);

            // Build query
            $query->whereNotIn('user_id', $blockedUserIds)
                ->whereNotIn('user_id', $reportedUserIds)
                ->whereIn('user_id', $fromUserIds)
                ->orderBy('created_at', 'DESC');

            return $query->get();
        } catch (\Exception $e) {
            // Write log
            $this->logInfo($e->getMessage());

            throw new DBException();
        }
    }
}
