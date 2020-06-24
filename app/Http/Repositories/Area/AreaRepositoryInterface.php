<?php

declare(strict_types=1);

namespace App\Repositories\Area;

interface AreaRepositoryInterface
{
    public function getAll($limit, $currentPage, $orderBy, $desc, $keyword);

    public function searchArea(); 

    public function searchPrefecture($lang, $areaCategory);
}
