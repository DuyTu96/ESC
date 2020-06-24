<?php

declare(strict_types=1);

namespace App\Repositories\AnomalousShopReservableTime;

use App\Models\AnomalousShopReservableTime as AnomaLous;
use App\Models\Shop;
use App\Repositories\RepositoryAbstract;

class AnomalousShopReservableTimeRepository extends RepositoryAbstract implements AnomalousShopReservableTimeRepositoryInterface
{
    public function __construct(AnomaLous $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->table = 'anomalous_shop_reservable_times';
    }

    /**
     * Delete and Create new Record
     */
    public function updateOrCreate($shopId, $listData)
    {
        $shop = Shop::find($shopId);
        if ($shop) {
            $shop->anomalous_shop_reservable_times()->delete();
            foreach($listData as $data) {
                $data['date_for_change'] = \Carbon\Carbon::parse($data['date_for_change'])->toDateTimeString();
                AnomaLous::create($data);
            }
        }
    }
}
