<?php

declare(strict_types=1);

namespace App\Repositories\Payment;

interface PaymentRepositoryInterface
{
    public function getList($request);
}
