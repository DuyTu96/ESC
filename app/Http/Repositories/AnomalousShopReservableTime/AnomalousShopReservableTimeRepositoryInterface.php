<?php

declare(strict_types=1);

namespace App\Repositories\AnomalousShopReservableTime;

interface AnomalousShopReservableTimeRepositoryInterface
{
    public function updateOrCreate($shopId, $listData);
}
