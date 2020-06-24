<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroupSaleSummary;

use App\Enums\Constant;
use App\Models\ShopGroupSalesSummary;
use App\Repositories\RepositoryAbstract;
use Auth;
use Carbon\Carbon;

class ShopGroupSaleSummaryRepository extends RepositoryAbstract implements ShopGroupSaleSummaryRepositoryInterface
{
    /**
     * Construct.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new ShopGroupSalesSummary;
        $this->table = 'shop_group_sales_summaries';
    }

    /**
     * Get OperatingCompanySalesSummary dashboard view.
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getByDateFromTo($startDate, $endDate)
    {
        $shopGroupId = Auth::user()->shop_group_id;
        $shopGroup = $this->model->where('shop_group_id', $shopGroupId)->select('*');

        if ($startDate) {
            $shopGroup = $shopGroup->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $shopGroup = $shopGroup->where('date', '<=', $endDate);
        }

        $count = $shopGroup->sum('reservation_sales');

        return [
            'total' => (int) $count,
            'items' => $shopGroup->get(),
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
            $dataByDays[$item['date']] = $item['reservation_sales'];
        }
        $labels = [];
        $chartData = [];
        $startDate = Carbon::createFromFormat('Y/m/d', $startDate);
        $endDate = Carbon::createFromFormat('Y/m/d', $endDate)->addDay();

        while ($startDate->diffInDays($endDate)) {
            $dateStr = $startDate->format('Y-m-d');
            $labels[] = Constant::DAY_OF_WEEK_JP[$startDate->dayOfWeek];
            $chartData[] = $dataByDays[$dateStr] ?? 0;
            $startDate->addDay();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $chartData,
                    'label' => '総売上',
                    'backgroundColor' => 'rgba(51,0,255,0.2)',
                ],
            ], ];
    }
}
