<?php

declare(strict_types=1);

namespace App\Repositories\Position;

use App\Repositories\EloquentRepository;

class PositionRepository extends EloquentRepository implements PositionRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Position::class;
    }

    public function getAllByCompanyId($companyId) 
    {   
        $positions = $this->model::where('company_id', $companyId)->orderBy('position_id', 'DESC')->get();
        
        return $positions;
    }

    public function createPosition($formData)
    {   
        $position = $this->create($formData);

        return $position;
    }

    public function deletePosition($id)
    {
        $position = $this->delete($id);

        return $position;
    }

    public function getAllPositionByCompanyId($companyId)
    {
        $positions = $this->model::select('position_id', 'name')->where('company_id', $companyId)->get();

        return $positions;
    }

    public function getPositionName($id)
    {
        $position = $this->find($id)->name;

        return $position;
    }
}
