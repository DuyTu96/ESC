<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Result;
use App\Models\WebPushNotificationContent;
use App\Models\WebPushSubscription;
use Carbon\Carbon;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService extends BaseService
{
    /**
     * Send push notification to an endpoint ARN.
     *
     * @param WebPushSubscription        $pushSubscription
     * @param WebPushNotificationContent $pushNotificationContent
     *
     * @return int
     */
    public function publishToEndpoint(
        WebPushSubscription $pushSubscription,
        WebPushNotificationContent $pushNotificationContent
    ) {
        $title = $pushNotificationContent->title;
        $message = $pushNotificationContent->body;

        try {
            $infoPushSub = json_decode($pushSubscription->push_subscription, true);
            $subscription = Subscription::create($infoPushSub, true);
            $auth = [
                'VAPID' => [
                    'subject' => 'mailto:' . env('MAIL_FROM_ADDRESS', 'noreply@196peace.com'),
                    'publicKey' => env('PUBLIC_KEY_PUSH_NOTIF'),
                    'privateKey' => env('PRIVATE_KEY_PUSH_NOTIF'),
                ],
            ];
            $webPush = new WebPush($auth);
            $data = [
                'title' => $title,
                'body' => $message,
                'url' => $pushNotificationContent->click_action,
            ];
            $webPush->sendNotification(
                $subscription,
                json_encode($data)
            );
            // handle eventual errors here, and remove the subscription from your server if it is expired
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if (!$report->isSuccess()) {
                    $pushSubscription->invalidated_at = Carbon::now();
                    $pushSubscription->save();
                }
            }
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());

            return Result::FAILURE;
        }
    }
}
