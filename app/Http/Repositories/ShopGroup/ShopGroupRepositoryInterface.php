<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroup;

interface ShopGroupRepositoryInterface
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
    public function getAll($limit, $currentPage, $keyword, $shopGroupId, $sortBy, $desc);

    /**
     * Get statistic by duration.
     * @author fabbi-hoibq
     * @param $request
     * @return array
     */
    public function getStatisticByDuration($request);

    /**
     * Get shopgroup by id.
     * @param id
     * @param mixed $id
     */
    public function show($id);
}
