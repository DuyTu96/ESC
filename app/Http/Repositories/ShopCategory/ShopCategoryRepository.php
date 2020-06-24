<?php

declare(strict_types=1);

namespace App\Repositories\ShopCategory;

use App\Enums\Constant;
use App\Enums\DBConstant;
use App\Models\ShopCategory;
use App\Repositories\RepositoryAbstract;

class ShopCategoryRepository extends RepositoryAbstract implements ShopCategoryRepositoryInterface
{
    /**
     * Get shop category data.
     * @author fabbi-hoibq
     * @param $request
     * @return array
     */
    public function getAll($request)
    {
        $limit = $request->query('limit') ?? Constant::BASIC_LIMIT;
        $currentPage = $request->query('current_page') ?? 1;
        $sortField = $request->query('sortBy') ?? 'shop_category_id';
        $sortType = ($request->query('isDescending') === "DESC") ? 'DESC' : 'ASC';

        $query = ShopCategory::select(
            '*',
            'basic_merchant_fee_rate AS basic_merchant_fee_rate_title',
            'is_vip_only AS is_vip_only_title',
            'target_type AS target_type_title',
            'is_reservable AS is_reservable_title'
        )->where('is_archived', DBConstant::ARCHIVE_FLAG_NOT_ARCHIVED)->orderBy($sortField, $sortType);

        $total = $query->count();
        $shopCategories = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();

        return [
            'shopCategories' => $shopCategories,
            'pagination' => [
                'total' => $total,
                'current_page' => $currentPage,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Update or create new shop category.
     * @author LinhNT
     * @param $data
     * */
    public function updateOrCreate($data): void
    {
        if (array_key_exists('shop_category_id', $data)) {
            $attributes = ['shop_category_id' => $data['shop_category_id']];
            ShopCategory::updateOrCreate($attributes, $data);
        } else {
            ShopCategory::updateOrCreate($data);
        }
    }

    /**
     * Update or create new shop category.
     * @author LinhNT
     * @param $id
     * */
    public function delete($id): void
    {
        $shopCategory = ShopCategory::find($id);
        if ($shopCategory != null) {
            $shopCategory->is_archived = DBConstant::ARCHIVE_FLAG_ARCHIVED;
            $shopCategory->save();
        }
    }

    /**
     * Search shop category by keyword.
     * @param $keyword
     */
    public function searchByKeyword($keyword)
    {
        $shopCategories = ShopCategory::where('category_name_ja', 'like', '%' . $keyword . '%')
            ->orWhere('category_name_ja', 'like', '%' . $keyword . '%')
            ->orWhere('category_name_cs', 'like', '%' . $keyword . '%')
            ->orWhere('category_name_ct', 'like', '%' . $keyword . '%')->limit(10)->get();

        return $shopCategories;
    }
}
