<?php

declare(strict_types=1);

namespace App\Repositories\PointReturnRequest;

use App\Enums\Constant;
use App\Enums\DBConstant;
use App\Models\PointReturnRequest;
use App\Models\User;
use App\Repositories\RepositoryAbstract;

class PointReturnRepository extends RepositoryAbstract implements PointReturnRepositoryInterface
{
    public function __construct(PointReturnRequest $pointReturnRequest)
    {
        parent::__construct();
        $this->model = $pointReturnRequest;
        $this->table = 'point_return_requests';
    }

    /**
     * Get point return list.
     * @param $request
     * @return array
     */
    public function getPointReturnList($request)
    {
        $limit = $request->query('limit') ?? Constant::BASIC_LIMIT;
        $currentPage = $request->query('current_page') ?? 1;
        $sortField = $request->query('sortBy') ?? 'user_id';
        $sortType = $request->query('isDescending') == 'DESC' ? 'DESC' : 'ASC';
        $query = PointReturnRequest::select(
            'users.user_id',
            'users.name AS user_name',
            'point_return_requests.reservation_id',
            'point_return_requests.point_return_request_id',
            'reservations.shop_id',
            'shops.shop_name_ja as shop_name',
            'point_return_requests.using_expense',
            'point_return_requests.reduction_rate',
            'point_return_requests.reward_points as reward_points_title'
        )->join('reservations', 'reservations.reservation_id', '=', 'point_return_requests.reservation_id')
            ->join('shops', 'shops.shop_id', '=', 'reservations.shop_id')
            ->join('users', 'users.user_id', '=', 'reservations.user_id')
            ->where('point_return_requests.approval_status', DBConstant::POINT_RETURN_REQUEST_APPROVAL_STATUS_NOT_APPROVED)
            ->orderBy($sortField, $sortType);

        $total = $query->count();

        return [
            'point_return_requests' => $query->limit($limit)->offset(($currentPage - 1) * $limit)->get(),
            'pagination' => [
                'total' => $total,
                'current_page' => $currentPage,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Find point return request by id.
     * @param $id
     * @return array
     */
    public function show($id)
    {
        $pointReturnRequest = PointReturnRequest::select(
            'users.user_id',
            'users.name',
            'point_return_requests.point_return_request_id',
            'reservations.shop_id',
            'shops.shop_name_ja',
            'point_return_requests.using_expense',
            'point_return_requests.reduction_rate',
            'point_return_requests.reward_points',
        )
            ->join('reservations', 'reservations.reservation_id', '=', 'point_return_requests.reservation_id')
            ->join('shops', 'shops.shop_id', '=', 'reservations.shop_id')
            ->join('users', 'users.user_id', '=', 'reservations.user_id')
            ->where('point_return_request_id', $id)
            ->get();

        return [
            'pointReturnRequest' => $pointReturnRequest,
        ];
    }

    /**
     * Approval point_return_request then add reward points to users.point_amount
     * @param $id
     * @return void
     */
    public function addRewardPointsToUser($id)
    {
        $pointReturnRequest = PointReturnRequest::find($id);
        $user = User::join('reservations as re', 're.user_id', 'users.user_id')
            ->join('point_return_requests as pr', 'pr.reservation_id', 're.reservation_id')
            ->where('pr.point_return_request_id', '=', $id)->first();

        if ($user) {
            $user->point_amount += (int)$pointReturnRequest->reward_points;
            $user->save();
        }
        return;
    }
}
