<?php

declare(strict_types=1);

namespace App\Repositories\Reservation;

use App\Enums\DBConstant;
use App\Enums\PerPageLimit;
use App\Models\Reservation;
use App\Repositories\RepositoryAbstract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReservationRepository extends RepositoryAbstract implements ReservationRepositoryInterface
{
    /**
     * Construct.
     *
     * @param Reservation     $reservation
     */
    public function __construct(Reservation $reservation)
    {
        parent::__construct();
        $this->model = $reservation;
    }

    /**
     * Get reservation list of user.
     *
     * @param User user
     * @param mixed $user
     * @return reservation list
     */
    public function getReservationListByUser($user)
    {
        return $this->model->select(
            'reservations.*',
            'shops.' . $this->getShopNameLangCode($user->lang_code) . ' as shop_name',
            'areas.' . $this->getAreaLangCode($user->lang_code) . ' as area_name',
            'shop_categories.' . $this->getShopCategoryLangCode($user->lang_code) . ' as category_name'
        )->join(
            'shops',
            'shops.shop_id',
            'reservations.shop_id'
        )->join(
            'areas',
            'areas.area_id',
            'shops.area_id'
        )->join(
            'shop_categories',
            'shop_categories.shop_category_id',
            'shops.shop_category_id'
        )->where('reservations.user_id', $user->user_id)->get();
    }

    /**
     * Get reservation detail.
     *
     * @param User user
     * @param int reservationId
     * @param mixed $user
     * @param mixed $reservationId
     * @return reservation
     */
    public function getReservationDetailByReservationId($user, $reservationId)
    {
        return $this->model->select(
            'reservations.*',
            'packages.minutes',
            'shops.' . $this->getShopNameLangCode($user->lang_code) . ' as shop_name',
            'areas.' . $this->getAreaLangCode($user->lang_code) . ' as area_name',
            'shops.' . $this->getTransportationLangCode($user->lang_code) . ' as transportation',
            'shop_categories.' . $this->getShopCategoryLangCode($user->lang_code) . ' as category_name',
            'packages.' . $this->getTitlePackageLangCode($user->lang_code) . ' as package_title',
        )->join(
            'shops',
            'shops.shop_id',
            'reservations.shop_id'
        )->join(
            'packages',
            'packages.package_id',
            'reservations.package_id'
        )->join(
            'areas',
            'areas.area_id',
            'shops.area_id'
        )->join(
                'shop_categories',
                'shop_categories.shop_category_id',
                'shops.shop_category_id'
            )->where('reservations.reservation_id', $reservationId)->first();
    }

    /**
     * Get reservation with sale report for OC reservations management.
     * @param $request
     * @return array
     * */
    public function getWithSaleReport($request)
    {
        $limit = $request->query('limit') ?? PerPageLimit::BASIC_LIMIT;
        $sortType = ($request->query('isDescending') === "DESC") ? 'DESC' : 'ASC';
        $currentPage = $request->query('current_page') ?? 1;
        if ($request->query('sortBy') === 'reservation_date_time') {
            $sortField = 'start_datetime';
        } elseif ($request->query('sortBy') === 'approval_status_title') {
            $sortField = 'approval_status';
        } elseif ($request->query('sortBy') === 'sale_reported_amount_title') {
            $sortField = 'sale_report_amount';
        } elseif ($request->query('sortBy') === 'sale_received_status_title') {
            $sortField = 'is_received';
        } else {
            $sortField = $request->query('sortBy') ?? 'reservation_id';
        }

        $whereSearch = [
            'reservation_id' => 'reservations.reservation_id', // 予約ID
            'shop_id' => 'reservations.shop_id', // 予約店舗ID
            'user_id' => 'reservations.user_id', // 予約ユーザーID
            'status' => 'reservations.approval_status', // 予約ステータス
            'sale_received_status' => 'sr.is_received', // 受取ステータス
        ];

        $whereLikeSearch = [
            'shop_name' => 's.shop_name_ja', // 予約店舗名
            'user_name' => 'u.name', // 予約ユーザー名
            'package_title' => 'p.title_ja', // 予約メニュー名
        ];

        $query = Reservation::select(
            'reservations.reservation_id',
            's.shop_id',
            's.shop_name_ja AS shop_name',
            'u.user_id',
            'u.name AS user_name',
            'p.title_ja AS package_title',
            'cm.name AS cast_member_name',
            'reservations.start_datetime',
            'reservations.end_datetime',
            'reservations.approval_status',
            'sr.correctable_deadline AS sale_correctable_deadline',
            'sr.id AS sales_reports_id',
            'sr.reported_amount as sale_report_amount',
            'sr.merchant_fee_rate AS sale_merchant_fee_rate',
            'sr.merchant_fee AS sale_merchant_fee',
            'sr.is_received AS sale_received_status',
            'sr.memo AS sale_memo'
        )
            ->join('shops AS s', 's.shop_id', '=', 'reservations.shop_id')
            ->join('users AS u', 'u.user_id', '=', 'reservations.user_id')
            ->join('packages AS p', 'p.package_id', '=', 'reservations.package_id')
            ->leftJoin('cast_members AS cm', 'cm.cast_member_id', '=', 'reservations.cast_member_id')
            ->join('sales_reports AS sr', 'sr.reservation_id', '=', 'reservations.reservation_id')
            ->orderBy($sortField, $sortType);

        $searchConditions = [];
        foreach ($whereSearch as $searchField => $searchColumn) {
            if ($request->query($searchField) !== null) {
                $searchConditions[$searchColumn] = trim($request->query($searchField));
                $query->where($searchColumn, trim($request->query($searchField)));
            }
        }

        $whereDateReservationsSearch = [
            'start_datetime' => 'reservations.start_datetime', // 予約日
            'end_datetime' => 'reservations.end_datetime', // 予約日
        ];
        if (!empty($request->query('start_datetime')) && !empty($request->query('end_datetime'))) {
            $startDate = Carbon::parse($request->query('start_datetime'))->toDateString();
            $endDate = Carbon::parse($request->query('end_datetime'))->toDateString();
            if ($startDate === $endDate) {
                $query->whereDate($whereDateReservationsSearch['start_datetime'], $startDate)
                    ->whereDate($whereDateReservationsSearch['end_datetime'], $endDate);
            } else {
                $query->whereDate($whereDateReservationsSearch['start_datetime'], '>=', $startDate)
                    ->whereDate($whereDateReservationsSearch['start_datetime'], '<', $endDate)
                    ->whereDate($whereDateReservationsSearch['end_datetime'], '<=', $endDate)
                    ->whereDate($whereDateReservationsSearch['end_datetime'], '>', $startDate);
            }
        }
        if (!empty($request->query('start_datetime')) && empty($request->query('end_datetime'))) {
            $startDate = Carbon::parse($request->query('start_datetime'))->toDateString();
            $query->whereDate($whereDateReservationsSearch['start_datetime'], '>=', $startDate);
        }
        if (empty($request->query('start_datetime')) && !empty($request->query('end_datetime'))) {
            $endDate = Carbon::parse($request->query('end_datetime'))->toDateString();
            $query->whereDate($whereDateReservationsSearch['end_datetime'], '<=', $endDate);
        }

        if (!empty($request->query('sale_deadline_from')) && !empty($request->query('sale_deadline_to'))) {
            $dateFrom = Carbon::parse($request->query('sale_deadline_from'))->toDateString();
            $dateTo = Carbon::parse($request->query('sale_deadline_to'))->toDateString();
            if ($dateFrom === $dateTo) {
                $query->whereDate('sr.correctable_deadline', $dateFrom);
            } else {
                $query->whereDate('sr.correctable_deadline', '>=', $dateFrom)
                    ->whereDate('sr.correctable_deadline', '<=', $dateTo);
            }
        }
        if (!empty($request->query('sale_deadline_from')) && empty($request->query('sale_deadline_to'))) {
            $dateFrom = Carbon::parse($request->query('sale_deadline_from'))->toDateString();
            $query->whereDate('sr.correctable_deadline', '>=', $dateFrom);
        }
        if (empty($request->query('sale_deadline_from')) && !empty($request->query('sale_deadline_to'))) {
            $dateFrom = Carbon::parse($request->query('sale_deadline_from'))->toDateString();
            $query->whereDate('sr.correctable_deadline', '>=', $dateFrom);
        }
        // 売上報告ステータス
        if (!empty($request->query('sale_reported_amount_status'))) {
            if ($request->query('sale_reported_amount_status') == 1) {
                $query->whereNull('sr.reported_amount');
            } else {
                $query->whereNotNull('sr.reported_amount');
            }
        }

        foreach ($whereLikeSearch as $searchField => $searchColumn) {
            if ($request->query($searchField) && trim($request->query($searchField))) {
                $searchConditions[$searchColumn] = trim($request->query($searchField));
                $query->where($searchColumn, 'like', '%' . trim($request->query($searchField)) . '%');
            }
        }

        $total = $query->get()->count();
        $data = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();

        return [
            'reservations' => $data,
            'pagination' => [
                'total' => $total,
                'current_page' => $currentPage,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @param mixed                    $data
     * @return \Illuminate\Http\Response
     */
    public function updateReservation($id, $data)
    {
        try {
            $currentApprovalStatus = optional($this->model->find($id))->approval_status;
            if (isset($data['approval_status'])) {
                // Check current approval status is not approved
                if ($currentApprovalStatus === DBConstant::RESERVATION_APPROVAL_STATUS_NOT_APPROVED) {
                    $now = Carbon::now()->toDateTimeString();

                    if ($data['approval_status'] === DBConstant::RESERVATION_APPROVAL_STATUS_REJECTED) {
                        $data['rejected_datetime'] = $now;
                    } elseif ($data['approval_status'] === DBConstant::RESERVATION_APPROVAL_STATUS_APPROVED) {
                        $data['approved_datetime'] = $now;
                    }
                } else {
                    return false;
                }
            }
            parent::update($id, $data);

            return true;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }

    /**
     * Get shop name langcode.
     *
     * @param string langCode
     * @param mixed $langCode
     */
    private function getShopNameLangCode($langCode)
    {
        switch ($langCode) {
            case DBConstant::LANG_CODE_JA:
                return 'shop_name_ja';
            case DBConstant::LANG_CODE_EN:
                return 'shop_name_en';
            case DBConstant::LANG_CODE_CS:
                return 'shop_name_cs';
            case DBConstant::LANG_CODE_CT:
                return 'shop_name_ct';
            default:
                return 'shop_name_en';
        }
    }

    /**
     * Get area name langcode.
     *
     * @param string langCode
     * @param mixed $langCode
     */
    private function getAreaLangCode($langCode)
    {
        switch ($langCode) {
            case DBConstant::LANG_CODE_JA:
                return 'area_name_ja';
            case DBConstant::LANG_CODE_EN:
                return 'area_name_en';
            case DBConstant::LANG_CODE_CS:
                return 'area_name_cs';
            case DBConstant::LANG_CODE_CT:
                return 'area_name_ct';
            default:
                return 'area_name_en';
        }
    }

    /**
     * Get shop category name langcode.
     *
     * @param string langCode
     * @param mixed $langCode
     */
    private function getShopCategoryLangCode($langCode)
    {
        switch ($langCode) {
            case DBConstant::LANG_CODE_JA:
                return 'category_name_ja';
            case DBConstant::LANG_CODE_EN:
                return 'category_name_en';
            case DBConstant::LANG_CODE_CS:
                return 'category_name_cs';
            case DBConstant::LANG_CODE_CT:
                return 'category_name_ct';
            default:
                return 'category_name_en';
        }
    }

    /**
     * Get package title langcode.
     *
     * @param string langCode
     * @param mixed $langCode
     */
    private function getTitlePackageLangCode($langCode)
    {
        switch ($langCode) {
            case DBConstant::LANG_CODE_JA:
                return 'title_ja';
            case DBConstant::LANG_CODE_EN:
                return 'title_en';
            case DBConstant::LANG_CODE_CS:
                return 'title_cs';
            case DBConstant::LANG_CODE_CT:
                return 'title_ct';
            default:
                return 'title_en';
        }
    }

    /**
     * Get transportation langcode.
     *
     * @param string langCode
     * @param mixed $langCode
     */
    private function getTransportationLangCode($langCode)
    {
        switch ($langCode) {
            case DBConstant::LANG_CODE_JA:
                return 'transportation_ja';
            case DBConstant::LANG_CODE_EN:
                return 'transportation_en';
            case DBConstant::LANG_CODE_CS:
                return 'transportation_cs';
            case DBConstant::LANG_CODE_CT:
                return 'transportation_ct';
            default:
                return 'transportation_en';
        }
    }

    /**
     * Get list reservation of user with point return request info
     * @param $userId
     * @param $langCode
     *
     * @return array
     * */
    public function getWithPointReturn($userId, $langCode)
    {
        return $this->model->select(
            'reservations.start_datetime',
            'shops.' . 'shop_name_' . $langCode . ' AS shop_name',
            'shop_categories.' . 'category_name_' . $langCode . ' AS category_name',
            'areas.' . 'area_name_' . $langCode  . ' as area_name',
            'COALESCE(point_return_requests.approval_status, 0) AS point_return_approval_status'
        )->join(
            'shops',
            'shops.shop_id',
            'reservations.shop_id'
        )->join(
            'areas',
            'areas.area_id',
            'shops.area_id'
        )->join(
            'shop_categories',
            'shop_categories.shop_category_id',
            'shops.shop_category_id'
        )->leftJoin(
            'point_return_requests',
            'point_return_requests.reservation_id',
            'reservations.reservation_id'
        )->where('reservations.user_id', $userId)->get();
    }

    /**
     * Get reservation of user with point return request info
     * @param $reservationId
     * @param $langCode
     *
     * @return stdClass
     * */
    public function getDetailWithPointReturn($reservationId, $langCode)
    {
        return $this->model->select(
            'reservations.start_datetime',
            'shops.' . 'shop_name_' . $langCode . ' AS shop_name',
            'shop_categories.' . 'category_name_' . $langCode . ' AS category_name',
            'areas.' . 'area_name_' . $langCode  . ' as area_name',
            'COALESCE(point_return_requests.approval_status, 0) AS point_return_approval_status'
        )->join(
            'shops',
            'shops.shop_id',
            'reservations.shop_id'
        )->join(
            'areas',
            'areas.area_id',
            'shops.area_id'
        )->join(
            'shop_categories',
            'shop_categories.shop_category_id',
            'shops.shop_category_id'
        )->leftJoin(
            'point_return_requests',
            'point_return_requests.reservation_id',
            'reservations.reservation_id'
        )->where('reservations.reservation_id', $reservationId)->first();
    }
}
