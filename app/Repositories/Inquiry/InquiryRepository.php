<?php

declare(strict_types=1);

namespace App\Repositories\Inquiry;

use App\Repositories\EloquentRepository;

class InquiryRepository extends EloquentRepository implements InquiryRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Inquiry::class;
    }
}
