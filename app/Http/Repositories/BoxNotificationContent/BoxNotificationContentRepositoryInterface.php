<?php

declare(strict_types=1);

namespace App\Repositories\BoxNotificationContent;

interface BoxNotificationContentRepositoryInterface
{
    public function getNotificationDetail($id, $lang);
}
