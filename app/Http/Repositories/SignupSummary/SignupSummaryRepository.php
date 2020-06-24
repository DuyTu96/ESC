<?php

declare(strict_types=1);

declare(strict_type=1);

namespace App\Repositories\SignupSummary;

use App\Enums\Constant;
use App\Models\SignupSummary;
use Carbon\Carbon;

class SignupSummaryRepository implements SignupSummaryRepositoryInterface
{
    public function getAll(): void
    {
    }

    /**
     * Get sign up summaries between 2 dates.
     * @author fabbi-hoibq
     *
     * @param $startDate
     * @param $endDate
     *
     * @return array
     */
    public function getByDateFromTo($startDate, $endDate)
    {
        $signups = SignupSummary::select('*');

        if ($startDate) {
            $signups = $signups->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $signups = $signups->where('date', '<=', $endDate);
        }

        $signupCount = $signups->sum('signup_count');
        $signups = $signups->get();

        return [
            'total' => (int) $signupCount,
            'items' => $signups,
        ];
    }

    /**
     * Calculate sign up summaries data for chart.
     * @param $days data for all day on search range
     * @return array
     * */
    public function calculateChartData($days)
    {
        $chartLabel = [];
        $chartData = [];
        foreach ($days as $day) {
            $date = Carbon::createFromFormat('Y-m-d', $day['date']);
            $chartLabel[] = Constant::DAY_OF_WEEK_JP[$date->dayOfWeek];
            $chartData[] = $day['signup_count'];
        }

        return [
            'labels' => $chartLabel,
            'datasets' => [['data' => $chartData, 'label' => '新規登録者数推移', 'backgroundColor' => 'rgba(255, 0, 0, 0, 0.2)']],
        ];
    }
}
