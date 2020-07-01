<?php

declare(strict_types=1);

namespace App\Repositories\ImagePath;

use App\Repositories\EloquentRepositoryInterface;

interface ImagePathRepositoryInterface extends EloquentRepositoryInterface
{
    public function checkImageExisted($object, $objectId);
}
