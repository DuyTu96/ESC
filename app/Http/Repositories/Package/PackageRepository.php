<?php

declare(strict_types=1);

namespace App\Repositories\Package;

use App\Models\Package;
use App\Repositories\RepositoryAbstract;
use App\Services\ImageService;

class PackageRepository extends RepositoryAbstract implements PackageRepositoryInterface
{
    protected $imageService;

    public function __construct(Package $package, ImageService $imageService)
    {
        parent::__construct();
        $this->model = $package;
        $this->table = 'packages';
        $this->imageService = $imageService;
    }

    /**
     * Get image by package_id.
     * @param mixed $package_id
     */
    public function getPackageWithImage($package_id)
    {
        $package = $this->model->with(['image' => function ($query) use ($package_id): void {
            $query->where(\DB::raw('SUBSTRING(image_paths.file_name, 1, 8)'), (int) $package_id);
        }])->find($package_id);

        // get url
        if (isset($package->image)) {
            $package->image->image_url = $this->imageService->getS3Url($package->image->dir_path . $package->image->file_name);
        }

        return $package;
    }
}
