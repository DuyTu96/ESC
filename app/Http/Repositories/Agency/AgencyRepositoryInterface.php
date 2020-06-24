<?php

declare(strict_types=1);

namespace App\Repositories\Agency;

interface AgencyRepositoryInterface
{
    public function getAgencyList($params, $request);

    public function updateOrCreate($data);
}
