<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroupUserMap;

use App\Models\ShopGroupUserMap;
use App\Repositories\RepositoryAbstract;

class ShopGroupUserMapRepository extends RepositoryAbstract implements ShopGroupUserMapRepositoryInterface
{
    protected $shopGroupUserMap;

    public function __construct(ShopGroupUserMap $shopGroupUserMap)
    {
        parent::__construct();
        $this->model = $shopGroupUserMap;
        $this->table = 'shop_group_user_maps';
    }

    /**
     * Update or create new shop_group_user_map.
     * @param $data
     * */
    public function updateOrCreate($data): void
    {
        if (array_key_exists('shop_id', $data) && $data['shop_id'] != null) {
            if (array_key_exists('sg_user_id', $data)) {
                $attributes = ['sg_user_id' => $data['sg_user_id']];
                ShopGroupUserMap::updateOrCreate($attributes, $data);
            } else {
                ShopGroupUserMap::create($data);
            }
        }
    }
}
