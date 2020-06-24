<?php

declare(strict_types=1);

namespace App\Repositories\SaleReport;

use App\Models\SalesReport;
use App\Repositories\RepositoryAbstract;

class SaleReportRepository extends RepositoryAbstract implements SaleReportRepositoryInterface
{
    /**
     * Construct.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new SalesReport;
        $this->table = 'sales_reports';
    }
}
