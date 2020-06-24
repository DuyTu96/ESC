<?php

declare(strict_types=1);

namespace App\Repositories\Shop;

interface ShopRepositoryInterface
{
    public function getShopMerchantFeeRateList($limit, $currentPage, $orderBy, $desc);

    public function getStatisticByDuration($request);

    public function getShopWithBillingAmount($filters, $select, $limit, $currentPage, $orderBy, $desc);

    public function getShopAreaSaleReportList($filters, $limit, $currentPage, $orderBy, $desc);

    public function getFavoriteShopByUser($shopId, $lang);

    public function getSearchResult($lang, $limit);

    public function getShopDetailWithBillingAmount($id);

    public function updateLicense(int $shopId, object $image, int $imagePathId);

    public function getShopsByCurrentUser($request);

    public function getListShopByAuthorityType($params);

    public function getNearByShops($request);

    public function getShopByKeyword($lang, $request);

}
