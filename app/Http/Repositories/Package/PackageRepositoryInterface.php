<?php

declare(strict_types=1);

namespace App\Repositories\Package;

interface PackageRepositoryInterface
{
    public function getPackageWithImage($package_id);
}
