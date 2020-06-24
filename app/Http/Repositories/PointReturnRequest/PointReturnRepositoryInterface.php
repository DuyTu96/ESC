<?php

declare(strict_types=1);

namespace App\Repositories\PointReturnRequest;

interface PointReturnRepositoryInterface
{
    public function getPointReturnList($request);

    public function show($id);

    public function addRewardPointsToUser($id);
}
