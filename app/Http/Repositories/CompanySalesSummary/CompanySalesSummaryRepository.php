<?php

declare(strict_types=1);

namespace App\Repositories\CompanySalesSummary;

use App\Enums\Constant;
use App\Models\OperatingCompanySalesSummary;
use Carbon\Carbon;

class CompanySalesSummaryRepository implements CompanySalesSummaryRepositoryInterface
{
    public function getAll(): void
    {
    }

    /**
     * Get OperatingCompanySalesSummary dashboard view.
     * @author skrum
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getByDateFromTo($startDate, $endDate)
    {
        $companySalesSummaries = OperatingCompanySalesSummary::select('*');

        if ($startDate) {
            $companySalesSummaries = $companySalesSummaries->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $companySalesSummaries = $companySalesSummaries->where('date', '<=', $endDate);
        }

        $count = $companySalesSummaries->sum('total_sales_amount');

        return [
            'total' => (int) $count,
            'items' => $companySalesSummaries->get(),
        ];
    }

    /**
     * Calculate reservation sale summaries data for chart.
     * @param $days: data for all day on search range
     * @return array
     * */
    public function calculateChartData($days)
    {
        $labels = [];
        $reservationSales = [];
        foreach ($days as $day) {
            $date = Carbon::createFromFormat('Y-m-d', $day['date']);
            $labels[] = Constant::DAY_OF_WEEK_JP[$date->dayOfWeek];
            $reservationSales[] = $day['reservation_sales'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                'data' => $reservationSales,
                'label' => '売上高推移',
                'backgroundColor' => 'rgba(51,0,255,0.2)'
                ],
            ]
        ];
    }
}
