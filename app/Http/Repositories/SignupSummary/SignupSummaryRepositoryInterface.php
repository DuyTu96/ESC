<?php

declare(strict_types=1);

namespace App\Repositories\SignupSummary;

interface SignupSummaryRepositoryInterface
{
    public function getAll();

    public function getByDateFromTo($startDate, $endDate);

    public function calculateChartData($days);
}
