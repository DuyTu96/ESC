<?php

declare(strict_types=1);

namespace App\Repositories\ImagePath;

use App\Repositories\EloquentRepository;

class ImagePathRepository extends EloquentRepository implements ImagePathRepositoryInterface
{   
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ImagePath::class;
    }

    public function checkImageExisted($object, $objectId)
    {
        $column = ($object == 'companies') ? 'company_id' : 'business_card_id';
        $imagePath = $this->model::where($column, $objectId)->first();

        return $imagePath;
    }
}
