<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroupUserMap;

interface ShopGroupUserMapRepositoryInterface
{
    public function updateOrCreate($data);
}
