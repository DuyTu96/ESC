<?php

declare(strict_types=1);

namespace App\Repositories\CompanyPvSummary;

interface CompanyPvSummaryRepositoryInterface
{
    public function getAll();

    public function getByDateFromTo($startDate, $endDate);

    public function calculateChartData($days);
}
