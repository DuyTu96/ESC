<?php

declare(strict_types=1);

namespace App\Repositories\Area;

use App\Models\Area;
use App\Repositories\RepositoryAbstract;
use Illuminate\Support\Facades\DB;
use App\Enums\ErrorType;

class AreaRepository extends RepositoryAbstract implements AreaRepositoryInterface
{
    public function __construct(Area $areaModel)
    {
        parent::__construct();
        $this->model = $areaModel;
        $this->table = 'areas';
    }

    /**
     * Get all area.
     * @param mixed $limit
     * @param mixed $currentPage
     * @param mixed $orderBy
     * @param mixed $desc
     * @param mixed $keyword
     */
    public function getAll($limit, $currentPage, $orderBy, $desc, $keyword)
    {
        $areas = $this->model;

        if ($keyword) {
            $areas = $areas->where('area_name_ja', 'like', '%' . $keyword . '%');
        }

        $areas = $areas->limit($limit)->offset(($currentPage - 1) * $limit)->orderBy($orderBy, $desc);

        $total = $areas->count();
        $areas = $areas->get();

        return [
            'areas' => $areas,
            'pagination' => [
                'limit' => $limit,
                'current_page' => $currentPage,
                'total' => $total,
            ],
        ];
    }

    /**
     * Get area search by area category
     * @author huydn
     * @return $areaCategory
     */
    public function searchArea() 
    {   
        $areaCategory = Area::select('areas.area_category', DB::raw('count(shops.shop_id)'))->join('shops', 'shops.area_id', '=', 'areas.area_id')->groupBy('areas.area_category')->get();

        return $areaCategory;
    }

    /**
     * Get prefecture search by area category
     * @author huydn
     * @param $request
     * @param $areaCategory
     * @return $shopPrefecture
     */
    public function searchPrefecture($lang, $areaCategory) 
    {   
        $areaName = 'area_name_' . $lang;
        $shopPrefecture = Area::where('area_category', $areaCategory)->select('areas.'. $areaName, 'areas.area_id', DB::raw('count(shops.shop_id)'))->join('shops', 'shops.area_id', '=', 'areas.area_id')->groupBy('areas.area_id')->orderBy('areas.display_order')->get();

        return $shopPrefecture;
    }
}
