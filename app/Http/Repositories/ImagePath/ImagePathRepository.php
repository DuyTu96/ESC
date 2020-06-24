<?php

declare(strict_types=1);

namespace App\Repositories\ImagePath;

use App\Models\ImagePath;
use App\Repositories\RepositoryAbstract;

class ImagePathRepository extends RepositoryAbstract implements ImagePathRepositoryInterface
{
    public function __construct(ImagePath $imagePath)
    {
        parent::__construct();
        $this->model = $imagePath;
        $this->table = 'image_paths';
    }
}
