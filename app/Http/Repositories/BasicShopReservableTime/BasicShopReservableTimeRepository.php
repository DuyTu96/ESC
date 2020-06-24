<?php

declare(strict_types=1);

namespace App\Repositories\BasicShopReservableTime;

use App\Models\BasicShopReservableTime;
use App\Repositories\RepositoryAbstract;

class BasicShopReservableTimeRepository extends RepositoryAbstract implements BasicShopReservableTimeRepositoryInterface
{
    /**
     * Construct.
     *
     * @param BoxNotification         $boxNotification
     * @param BasicShopReservableTime $basicShopReservableTimeModel
     */
    public function __construct(BasicShopReservableTime $basicShopReservableTimeModel)
    {
        parent::__construct();
        $this->model = $basicShopReservableTimeModel;
        $this->table = 'basic_shop_reservable_times';
    }
}
