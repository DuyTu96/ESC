<?php

declare(strict_types=1);

namespace App\Repositories\Position;

use App\Repositories\EloquentRepositoryInterface;

interface PositionRepositoryInterface extends EloquentRepositoryInterface
{
    public function getAllByCompanyId($companyId);

    public function createPosition($formData);

    public function deletePosition($id);

    public function getAllPositionByCompanyId($companyId);

    public function getPositionName($id);
}
