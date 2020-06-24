<?php

declare(strict_types=1);

namespace App\Repositories\Shop;

use App\Enums\DBConstant;
use App\Enums\PerPageLimit;
use App\Models\ImagePath;
use App\Models\Shop;
use App\Repositories\RepositoryAbstract;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopRepository extends RepositoryAbstract implements ShopRepositoryInterface
{
    protected $imageService;

    public function __construct(Shop $shopModel, ImageService $imageService)
    {
        parent::__construct();
        $this->model = $shopModel;
        $this->imageService = $imageService;
        $this->table = 'shops';
    }

    /**
     * Get shop merchant fee list.
     * @param $limit, $current_page, $orderBy, $desc
     * @param mixed $current_page
     * @param mixed $orderBy
     * @param mixed $desc
     * @return data of shop merchant fee list
     */
    public function getShopMerchantFeeRateList($limit, $current_page, $orderBy, $desc)
    {
        $data = Shop::select(
            'shops.shop_id',
            'shops.shop_name_ja',
            'shop_groups.shop_group_id',
            'shop_groups.name',
            'shops.shop_category_id',
            'shop_categories.category_name_ja',
            'areas.area_name_ja',
            'shop_categories.basic_merchant_fee_rate',
        )
            ->join('shop_groups', 'shop_groups.shop_group_id', '=', 'shops.shop_group_id')
            ->join('shop_categories', 'shop_categories.shop_category_id', '=', 'shops.shop_category_id')
            ->join('areas', 'areas.area_id', '=', 'shops.area_id')
            ->where('shops.approval_status', '=', DBConstant::SHOP_APPROVAL_STATUS_NOT_APPROVED);
        $total = $data->count();
        $data = $data->orderBy($orderBy, $desc)->limit($limit)->offset(($current_page - 1) * $limit);
        return [
            'shopMerchants' => $data->get(),
            'pagination' => [
                'total' => (int) $total,
                'current_page' => (int) $current_page,
                'limit' => (int) $limit,
            ],
        ];
    }

    /**
     * Get shops data with billing amount.
     * @author fabbi-hoibq
     * @param
     * @param mixed $filters
     * @param mixed $select
     * @param mixed $limit
     * @param mixed $currentPage
     * @param mixed $orderBy
     * @param mixed $desc
     * @return
     */
    public function getShopWithBillingAmount($filters, $select, $limit, $currentPage, $orderBy, $desc)
    {
        $shopBillings = Shop::select($select)
            ->join('shop_groups', 'shop_groups.shop_group_id', '=', 'shops.shop_group_id')
            ->join('payments', 'payments.shop_id', '=', 'shops.shop_id');
        foreach ($filters as $key => $where) {
            if (!$where['value']) {
                continue;
            }
            if ($where['where'] == 'like') {
                $shopBillings = $shopBillings->where($key, 'like', '%' . $where['value'] . '%');
            } elseif ($where['where'] == '=') {
                $shopBillings = $shopBillings->where($key, '=', $where['value']);
            }
        }
        $total = $shopBillings->count();
        $shopBillings = $shopBillings->orderBy($orderBy, $desc)->limit($limit)->offset(($currentPage - 1) * $limit);
        return [
            'shop_billings' => $shopBillings->get(),
            'pagination' => [
                'total' => (int) $total,
                'currentPage' => (int) $currentPage,
                'limit' => (int) $limit,
            ],
        ];
    }

    /**
     * Get shop data by duration.
     * @author fabbi-hoibq
     * @param $request
     * @return array
     */
    public function getStatisticByDuration($request)
    {
        $limit = $request['limit'] ?? PerPageLimit::BASIC_LIMIT;
        $currentPage = $request['current_page'] ?? 1;
        $keyword = $request['keyword'] ?? null;
        $startDate = $request['start_date'] ?? now()->subDays(7)->format('Y-m-d');
        $endDate = $request['end_date'] ?? now()->addDay(7)->format('Y-m-d');
        $sortBy = $request['sortBy'] ?? 'shop_name_ja';
        $sortType = $request['isDescending'] === 'DESC' ? 'DESC' : 'ASC';

        $shops = Shop::select(
            'shops.shop_id',
            'shops.shop_name_ja',
            DB::raw("COALESCE((SELECT SUM(sss.reservation_sales) FROM shop_sales_summaries AS sss WHERE sss.shop_id = shops.shop_id AND sss.date BETWEEN '$startDate' AND '$endDate'), 0) AS reservation_sales"),
            DB::raw("COALESCE((SELECT SUM(sps.page_views) FROM shop_pv_summaries AS sps WHERE sps.shop_id = shops.shop_id AND sps.date BETWEEN '$startDate' AND '$endDate'), 0) AS page_views")
        );

        if ($keyword != null) {
            $shops->where('shops.shop_name_ja', 'like', '%' . $keyword . '%');
        }
        $total = $shops->count();
        $shops = $shops->orderBy($sortBy, $sortType)->limit($limit)->offset(($currentPage - 1) * $limit);
        return [
            'shops' => $shops->get(),
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'current_page' => $currentPage,
            ],
        ];
    }

    /**
     * get Area sale report.
     * @author fabbi-sonbt
     * @param $filters, $limit, $currentPage, $orderBy, $desc
     * @param mixed $limit
     * @param mixed $currentPage
     * @param mixed $orderBy
     * @param mixed $desc
     * @return
     */
    public function getShopAreaSaleReportList($filters, $limit, $currentPage, $orderBy, $desc)
    {
        $data = Shop::select('shops.*', 'shops.merchant_fee_rate as percent_merchant_fee_rate', 'shop_groups.shop_group_id', 'shop_groups.name', 'shop_categories.category_name_ja', 'areas.area_name_ja')
            ->join('shop_groups', 'shop_groups.shop_group_id', '=', 'shops.shop_group_id')
            ->join('shop_categories', 'shop_categories.shop_category_id', 'shops.shop_category_id')
            ->join('areas', 'areas.area_id', '=', 'shops.area_id');
        foreach ($filters as $key => $where) {
            if (!$where['value']) {
                continue;
            }
            if ($where['where'] == 'like') {
                $data = $data->where($key, 'like', '%' . $where['value'] . '%');
            } elseif ($where['where'] == '=') {
                $data = $data->where($key, '=', $where['value']);
            }
        }
        $total = $data->count();
        $shops = $data->orderBy($orderBy, $desc)->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        return [
            'total' => $total,
            'shops' => $shops,
        ];
    }

    /**
     * Get data to Search result screen.
     * @param $lang (language is choosen by user)
     * @param mixed $limit
     * @return
     */
    public function getSearchResult($lang, $limit)
    {
        $result = Shop::select(
            'shops.shop_id',
            'shops.shop_name_' . $lang,
            'shops.transportation_' . $lang,
            'shops.description_' . $lang,
            'shop_categories.category_name_' . $lang,
            'areas.area_name_' . $lang,
        )
            ->with('image_paths')
            ->join('shop_categories', 'shops.shop_category_id', '=', 'shop_categories.shop_category_id')
            ->join('areas', 'shops.area_id', '=', 'areas.area_id')
            ->limit($limit)
            ->get();

        $result->map(function($value) {
            $imagePath = $value->image_paths->first();
            if (isset($imagePath)) {
                $value['image_path'] = $this->imageService->getS3Url($imagePath->dir_path . $imagePath->file_name);
            }
        });

        $data = [
            'data' => $result,
            'lang' => $lang
        ];

        return $data;
    }

    /**
     * Get favorite shop of auth user.
     * @param $shopId(array), $lang
     * @param mixed $lang
     * @return favorite shop of auth user
     */
    public function getFavoriteShopByUser($shopId, $lang)
    {
        $favoriteShops = Shop::select(
            'shops.shop_name_' . $lang,
            'shops.transportation_' . $lang,
            'shop_categories.category_name_' . $lang,
            'areas.area_name_' . $lang,
        )
            ->join('shop_categories', 'shops.shop_category_id', '=', 'shop_categories.shop_category_id')
            ->join('areas', 'shops.area_id', '=', 'areas.area_id')
            ->whereIn('shops.shop_id', $shopId)->get();
        return $favoriteShops;
    }

    /**
     * Get shop detail with billing amount by payment_id.
     * @param $id
     * @return array data
     */
    public function getShopDetailWithBillingAmount($id)
    {
        $select = [
            'shops.*',
            'shop_groups.shop_group_id',
            'shop_groups.name as shop_group_name',
            'payments.id as payment_id',
            'payments.closing_date',
            'payments.payment_date',
            'payments.is_paid',
            'payments.from_oc_to_shop',
        ];
        $data = Shop::select($select)
            ->join('shop_groups', 'shop_groups.shop_group_id', '=', 'shops.shop_group_id')
            ->join('payments', 'payments.shop_id', '=', 'shops.shop_id')
            ->where('payments.id', $id)
            ->get();
        return [
            'shopBillingDetail' => $data,
        ];
    }

    /**
     * Update/create image license.
     * @param int      $shopId
     * @param object   $image
     * @param null|int $imagePathId
     */
    public function updateLicense(int $shopId, object $image, int $imagePathId = null): void
    {
        if ($imagePathId) {
            $imagePath = ImagePath::find($imagePathId);
            $this->imageService->updateImageInS3($shopId, $imagePathId, $image, DBConstant::IMAGE_TYPE_SHOP_LICENSE, $imagePath->display_order, $shopId);
        } else {
            $this->imageService->uploadImageToS3($shopId, $image, 2, ImagePath::max('display_order') + 1, $shopId);
        }
    }

    /**
     * Get list Shop by authority_type.
     * @param $params
     * @return array
     */
    public function getListShopByAuthorityType($params)
    {
        $authorityType = Auth::user()->authority_type;

        $userId = Auth::id();
        $select = $params['select'] ?? ['*'];
        $limit = $params['limit'];
        $currentPage = $params['currentPage'];
        $sortBy = $params['sortBy'];
        $desc = $params['desc'];
        if ($authorityType == DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR) {
            $shopGroupId = Auth::user()->shop_group_id;
            $shops = Shop::select($select)->where('shop_group_id', $shopGroupId);
        } else {
            $shops = Shop::select($select)
                ->join('shop_group_user_maps', 'shop_group_user_maps.shop_id', 'shops.shop_id')
                ->where('shop_group_user_maps.sg_user_id', $userId);
        }
        $shops = $shops->where('approval_status', '=', DBConstant::SHOP_APPROVAL_STATUS_APPROVED);
        $total = $shops->count();
        $shops = $shops->limit($limit)->offset(($currentPage - 1) * $limit)->orderBy($sortBy, $desc)->get();

        return [
            'shops' => $shops,
            'pagination' => [
                'total' => (int) $total,
                'current_page' => (int) $currentPage,
                'limit' => (int) $limit,
            ],
        ];
    }

    /**
     * Get shop by shop_group.
     * @param $request
     * @return array[Shop]
     */
    public function getShopsByCurrentUser($request)
    {
        $shops = Shop::where('shop_group_id', $request->user()->shop_group_id)->get();
        return $shops;
    }

    /**
     * Get ale and page view of all shop in shop group
     * @param $request
     * @return array
     */
    public function getShopsOfShopGroup($request)
    {
        $sortBy = $request['sortBy'] ?? 'shop_name_ja';
        $desc = ($request['isDescending'] === 'DESC') ? 'DESC' : 'ASC';
        $shopGroupId = Auth::user()->shop_group_id;
        $startDate = $request['start_date'] ?? now()->subDays(7)->format('Y-m-d');
        $endDate = $request['end_date'] ?? now()->addDay(7)->format('Y-m-d');
        $pageViewSubQuery = "COALESCE((SELECT SUM(page_views) FROM shop_pv_summaries sps WHERE sps.shop_id = shops.shop_id AND sps.date BETWEEN '$startDate' AND '$endDate' GROUP BY sps.shop_id), 0) AS page_view";
        $saleSummarySubQuery = "COALESCE((SELECT SUM(reservation_sales) FROM shop_sales_summaries sss WHERE sss.shop_id = shops.shop_id AND sss.date BETWEEN '$startDate' AND '$endDate' GROUP BY sss.shop_id), 0) AS sale_summary";
        $shops = $this->model->where('shop_group_id', $shopGroupId)
            ->withTrashed()
            ->select(
                'shops.shop_id',
                'shops.shop_name_ja',
                DB::raw($pageViewSubQuery),
                DB::raw($saleSummarySubQuery)
            )->orderBy($sortBy, $desc);


        if ($request['keyword']) {
            $shops->where('shops.shop_name_ja', 'like', '%' . $request['keyword'] . '%');
        }

        $total = $shops->get()->count();

        $currentPage = $request['current_page'] ?? 1;
        $limit = $request['limit'] ?? PerPageLimit::BASIC_LIMIT;
        $shops->limit($limit)->offset(($currentPage - 1) * $limit);
        $shops = $shops->get();

        return [
            'shops' => $shops,
            'pagination' => [
                'total' => (int) $total,
                'limit' => (int) $limit,
                'current_page' => (int) $currentPage,
            ],
        ];
    }

    /**
     * get all shop by shop_group_id.
     * @param $request
     * @return shops
     * @return pagination
     */
    public function shopDetail($request)
    {
        $shops = Auth::user()->shops;
        $shopId = $shops->first()->shop_id ?? null;
        if (!empty($shopId)) {
            $now = now();
            $limit = $request['limit'] ?? PerPageLimit::BASIC_LIMIT;
            $currentPage = $request['current_page'] ?? 1;
            $keyword = $request['keyword'] ?? '';
            $startDate = $request['start_date'] ?? $now->subDays(7)->format('Y/m/d');
            $endDate = $request['end_date'] ?? $now->addDay(7)->format('Y/m/d');
            $sortBy = $request['sortBy'] ?? 'shop_name_ja';
            $desc = $request['isDescending'] ? 'DESC' : 'ASC';
            $shopData = $this->model->where('shops.shop_id', $shopId)->select(
                'shops.shop_id',
                'shops.shop_name_ja',
                \DB::raw('SUM(shop_sales_summaries.reservation_sales) as reservation_sales'),
                \DB::raw('SUM(shop_pv_summaries.page_views) as page_views')
            )
                ->join('shop_sales_summaries', 'shop_sales_summaries.shop_id', '=', 'shops.shop_id')
                ->join('shop_pv_summaries', 'shop_pv_summaries.shop_id', '=', 'shops.shop_id');
            $shopData->groupBy('shops.shop_id');
            if ($keyword) {
                $shopData->where('shops.shop_name_ja', 'like', '%' . $keyword . '%');
            }
            if (!empty($startDate) && !empty($endDate)) {
                $shopData->whereBetween('shop_sales_summaries.date', [$startDate, $endDate])
                    ->whereBetween('shop_pv_summaries.date', [$startDate, $endDate]);
            }
            if (!empty($startDate) && empty($endDate)) {
                $shopData->whereDate('shop_sales_summaries.date', '>=', $startDate)
                    ->whereDate('shop_pv_summaries.date', '>=', $startDate);
            }
            if (empty($startDate) && !empty($endDate)) {
                $shopData->whereDate('shop_sales_summaries.date', '<=', $endDate)
                    ->whereDate('shop_pv_summaries.date', '<=', $endDate);
            }
            $total = $shopData->get()->count();
            $shopData->orderBy($sortBy, $desc)->limit($limit)->offset(($currentPage - 1) * $limit);
            return [
                'shops' => $shopData->get(),
                'duration' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'pagination' => [
                    'total' => (int) $total,
                    'limit' => (int) $limit,
                    'current_page' => (int) $currentPage,
                ],
            ];
        }
    }

    /**
     * Get near by shop within 1km.
     * @author huydn
     * @param $request
     * @return $nearbyShops
     */
    public function getNearByShops($request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $nearbyShops = Shop::whereBetween('latitude', [$lat - DBConstant::DEFAULT_LATITUDE_SEARCH_RADIUS, $lat +  DBConstant::DEFAULT_LATITUDE_SEARCH_RADIUS])
                           ->whereBetween('longitude', [$lng - DBConstant::DEFAULT_LONGITUDE_SEARCH_RADIUS, $lng + DBConstant::DEFAULT_LONGITUDE_SEARCH_RADIUS])
                           ->get();
        return $nearbyShops;
    }

    /**
     * Get shop by keyword.
     * @author huydn
     * @param $lang
     * @param $request
     * @return $shopResult
     */
    public function getShopByKeyword($lang, $request)
    {
        $keyword = $request->query('keyword');
        $searchBy = 'keywords_'.$lang;
        $shopResult = Shop::where($searchBy, 'like', '%'.$keyword.'%')->get();
        return $shopResult;
    }

    /**
     * search shop by shop_name_ja.
     * @param $request
     * @return $shopResult
     */
    public function searchShop($request)
    {
        $keyword = $request->query('keyword');
        $shopResult = Shop::where('shop_name_ja', 'like', '%'.$keyword.'%')->select('shop_id', 'shop_name_ja')->limit(10)->get();

        return $shopResult;
    }
}
