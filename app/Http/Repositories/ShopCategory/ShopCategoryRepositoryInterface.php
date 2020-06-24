<?php

declare(strict_types=1);

namespace App\Repositories\ShopCategory;

interface ShopCategoryRepositoryInterface
{
    public function getAll($request);

    public function updateOrCreate($data);

    public function delete($id);

    public function searchByKeyword($keyword);
}
