<?php

declare(strict_types=1);

namespace App\Repositories\EmailContent;

use App\Models\EmailContent;
use App\Repositories\RepositoryAbstract;

class EmailContentRepository extends RepositoryAbstract implements EmailContentRepositoryInterface
{
    /**
     * Construct.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new EmailContent;
        $this->table = 'email_contents';
    }

    /**
     * Store.
     *
     * @param array $data
     *
     * @return
     */
    public function store($data)
    {
        return $this->model->create($data);
    }
}
