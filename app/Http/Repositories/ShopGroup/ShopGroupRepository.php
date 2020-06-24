<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroup;

use App\Enums\PerPageLimit;
use App\Models\ShopGroup;
use Illuminate\Support\Facades\DB;

class ShopGroupRepository implements ShopGroupRepositoryInterface
{
    /**
     * Get all shop groups.
     * @author fabbi-hoibq
     * @param mixed $limit
     * @param mixed $currentPage
     * @param mixed $keyword
     * @param mixed $shopGroupId
     * @param mixed $sortBy
     * @param mixed $desc
     */
    public function getAll($limit, $currentPage, $keyword, $shopGroupId, $sortBy, $desc)
    {
        $shopGroups = ShopGroup::select('*');

        if ($keyword) {
            $shopGroups = $shopGroups->where('name', 'like', '%' . $keyword . '%');
        }

        if ($shopGroupId) {
            $shopGroups = $shopGroups->where('shop_group_id', $shopGroupId);
        }

        $total = $shopGroups->count();
        $shopGroups = $shopGroups->orderBy($sortBy, $desc)->limit($limit)->offset(($currentPage - 1) * $limit);

        return [
            'shopGroups' => $shopGroups->get(),
            'pagination' => [
                'total' => (int) $total,
                'limit' => (int) $limit,
                'currentPage' => (int) $currentPage,
            ],
        ];
    }

    /**
     * Get Shop group by duration.
     * @author fabbi-hoibq
     * @param $request
     * @return array
     */
    public function getStatisticByDuration($request)
    {
        $limit = $request->query('limit') ?? PerPageLimit::BASIC_LIMIT;
        $currentPage = $request->query('current_page') ?? 1;
        $keyword = $request->query('keyword') ?? null;
        $startDate = $request->query('start_date') ?? now()->subDays(7)->format('Y-m-d');
        $endDate = $request->query('end_date') ?? now()->addDay(7)->format('Y-m-d');
        $sortBy = $request->query('sortBy') ?? 'name';
        $sortType = $request->query('isDescending') === 'DESC' ? 'DESC' : 'ASC';

        $query = ShopGroup::select(
            'shop_groups.shop_group_id',
            'shop_groups.name',
            DB::raw("COALESCE((SELECT SUM(sgps.page_views) FROM shop_group_pv_summaries AS sgps WHERE sgps.shop_group_id = shop_groups.shop_group_id AND sgps.date BETWEEN '$startDate' AND '$endDate'), 0) AS page_views"),
            DB::raw("COALESCE((SELECT SUM(sgss.reservation_sales) FROM shop_group_sales_summaries AS sgss WHERE sgss.shop_group_id = shop_groups.shop_group_id AND sgss.date BETWEEN '$startDate' AND '$endDate'), 0) AS reservation_sales")
        );
        
        if ($keyword != null) {
            $query->where('shop_groups.name', 'like', '%' . $keyword . '%');
        }

        $total = $query->count();
        $shopGroups = $query->orderBy($sortBy, $sortType)->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        return [
            'shopGroups' => $shopGroups,
            'pagination' => [
                'limit' => $limit,
                'current_page' => $currentPage,
                'total' => $total,
            ],
        ];
    }

    /**
     * Get shopgroup by id.
     * @param id
     * @param mixed $id
     */
    public function show($id)
    {
        $shopGroup = ShopGroup::find($id);

        return [
            'shopGroup' => $shopGroup,
        ];
    }

    /**
     * Search shop group by name.
     * @param $keyword
     */
    public function searchShopGroup($keyword)
    {
        if (trim($keyword) == 'null') {
            $shopCategories = ShopGroup::limit(10)->get();
        } else {
            $shopCategories = ShopGroup::where('name', 'like', '%' . $keyword . '%')->limit(10)->get();
        }

        return $shopCategories;
    }
}
