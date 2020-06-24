<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroupPvSummary;

use App\Enums\Constant;
use App\Models\ShopGroupPvSummary;
use Carbon\Carbon;
use Auth;

class ShopGroupPvSummaryRepository implements ShopGroupPvSummaryRepositoryInterface
{

    /**
     * Get OperatingCompanyPvSummary dashboard view.
     * @author skrum
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getByDateFromTo($startDate, $endDate)
    {
        $query = ShopGroupPvSummary::select('*');
        $query->where('shop_group_id', Auth::user()->shop_group_id);
        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        return [
            'total' => (int) $query->sum('page_views'),
            'items' => $query->get(),
        ];
    }

    /**
     * Calculate sign up summaries data for chart.
     * @param $data
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function calculateChartData($data, $startDate, $endDate)
    {
        $dataByDays = [];
        foreach ($data as $item) {
            $dataByDays[$item['date']] = $item;
        }
        $labels = [];
        $chartData = [];
        $startDate = Carbon::createFromFormat('Y/m/d', $startDate);
        $endDate = Carbon::createFromFormat('Y/m/d', $endDate)->addDay();
        while ($startDate->diffInDays($endDate)) {
            $dateStr = $startDate->format('Y-m-d');
            $labels[] = Constant::DAY_OF_WEEK_JP[$startDate->dayOfWeek];
            $chartData[] = $dataByDays[$dateStr]['page_views'] ?? 0;
            $startDate = $startDate->addDay();
        }

        return ['labels' => $labels, 'datasets' => [
            ['data' => $chartData, 'label' => 'PV数', 'backgroundColor' => 'rgba(51,0,255,0.2)'],
        ]];
    }
}
