<?php

declare(strict_types=1);

namespace App\Repositories\Department;

use App\Repositories\EloquentRepository;

class DepartmentRepository extends EloquentRepository implements DepartmentRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Department::class;
    }

    public function getAllByCompanyId($companyId) 
    {
        $departments = $this->model::where('company_id', $companyId)->orderBy('department_id', 'DESC')->get();

        return $departments;
    }

    public function createDepartment($formData) 
    {   
        $department = $this->create($formData);

        return $department;
    }

    public function deleteDepartment($id)
    {
        $department = $this->delete($id);

        return $department;
    }

    public function getAllDepartmentByCompanyId($companyId)
    {
        $departments = $this->model::select('department_id', 'name')->where('company_id', $companyId)->get();

        return $departments;
    }

    public function getDepartmentName($id)
    {
        $department = $this->find($id)->name;

        return $department;
    }
}
