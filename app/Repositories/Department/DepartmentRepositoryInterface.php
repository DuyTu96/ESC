<?php

declare(strict_types=1);

namespace App\Repositories\Department;

use App\Repositories\EloquentRepositoryInterface;

interface DepartmentRepositoryInterface extends EloquentRepositoryInterface
{
    public function getAllByCompanyId($companyId);

    public function createDepartment($formData);

    public function deleteDepartment($id);

    public function getAllDepartmentByCompanyId($companyId);

    public function getDepartmentName($id);
}
