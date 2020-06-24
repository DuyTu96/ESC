<?php

declare(strict_types=1);

namespace App\Repositories\PushNotificationContent;

use App\Models\WebPushNotificationContent;
use App\Repositories\RepositoryAbstract;

class PushNotificationContentRepository extends RepositoryAbstract implements PushNotificationContentRepositoryInterface
{
    /**
     * Construct.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new WebPushNotificationContent;
        $this->table = 'web_push_notification_contents';
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
