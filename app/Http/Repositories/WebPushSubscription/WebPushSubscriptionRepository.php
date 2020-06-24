<?php

declare(strict_types=1);

namespace App\Repositories\WebPushSubscription;

use App\Models\WebPushSubscription;
use App\Repositories\RepositoryAbstract;

class WebPushSubscriptionRepository extends RepositoryAbstract implements WebPushSubscriptionRepositoryInterface
{
    /**
     * Construct.
     *
     * @param WebPushSubscription $webPushSubscription
     */
    public function __construct(WebPushSubscription $webPushSubscription)
    {
        parent::__construct();
        $this->model = $webPushSubscription;
        $this->table = 'web_push_subscriptions';
    }

    public function getWebPushSubscriptionByPushSubscription($data)
    {
        $webPushSubscription = $this->model->where('push_subscription', $data['push_subscription'])
            ->first();
        if ($webPushSubscription) {
            $webPushSubscription['user_id'] = $data['user_id'];
            $webPushSubscription->save();
        }

        return $webPushSubscription;
    }

    public function deleteByUserIdAndSub($data)
    {
        return $this->model->where('user_id', $data['user_id'])
            ->where('push_subscription', $data['push_subscription'])
            ->delete();
    }
}
