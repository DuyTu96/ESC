<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroupPvSummary;

interface ShopGroupPvSummaryRepositoryInterface
{
    public function getByDateFromTo($startDate, $endDate);

    public function calculateChartData($days, $startDate, $endDate);
}
