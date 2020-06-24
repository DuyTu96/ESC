<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroupSaleSummary;

use App\Repositories\RepositoryInterface;

interface ShopGroupSaleSummaryRepositoryInterface extends RepositoryInterface
{
    public function getByDateFromTo($startDate, $endDate);

    public function calculateChartData($date, $startDate, $endDate);
}
