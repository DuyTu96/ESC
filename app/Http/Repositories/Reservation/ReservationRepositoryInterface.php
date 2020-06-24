<?php

declare(strict_types=1);

namespace App\Repositories\Reservation;

use App\Repositories\RepositoryInterface;

interface ReservationRepositoryInterface extends RepositoryInterface
{
    public function getWithSaleReport($request);

    public function getWithPointReturn($userId, $langCode);

    public function getDetailWithPointReturn($reservationId, $langCode);
}
