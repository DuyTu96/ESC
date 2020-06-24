<?php

declare(strict_types=1);

namespace App\Repositories\CompanyPvSummary;

use App\Enums\Constant;
use App\Models\OperatingCompanyPvSummary;
use Carbon\Carbon;

class CompanyPvSummaryRepository implements CompanyPvSummaryRepositoryInterface
{
    public function getAll(): void
    {
    }

    /**
     * Get OperatingCompanyPvSummary dashboard view.
     * @author skrum
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getByDateFromTo($startDate, $endDate)
    {
        $companyPvSummaries = OperatingCompanyPvSummary::select('*');

        if ($startDate) {
            $companyPvSummaries = $companyPvSummaries->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $companyPvSummaries = $companyPvSummaries->where('date', '<=', $endDate);
        }

        return [
            'total' => (int) $companyPvSummaries->sum('page_views'),
            'items' => $companyPvSummaries->get(),
        ];
    }

    /**
     * Calculate sign up summaries data for chart.
     * @param $days data for all day on search range
     * @return array
     * */
    public function calculateChartData($days)
    {
        $labels = [];
        $pgViews = [];
        foreach ($days as $day) {
            $date = Carbon::createFromFormat('Y-m-d', $day['date']);
            $labels[] = Constant::DAY_OF_WEEK_JP[$date->dayOfWeek];
            $pgViews[] = $day['page_views'];
        }

        return ['labels' => $labels, 'datasets' => [
            ['data' => $pgViews, 'label' => 'PV推移', 'backgroundColor' => 'rgba(51,0,255,0.2)'],
        ]];
    }
}
