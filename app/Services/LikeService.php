<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Email;
use App\Models\EmailContent;
use App\Models\Like;
use App\Models\NotificationSetting;
use App\Models\PushNotification;
use App\Models\PushNotificationContent;
use Carbon\Carbon;

class LikeService
{
    public function createNotification(Like $like): void
    {
        if ($like) {
            $today = Carbon::now();
            $notification_setting = NotificationSetting::where('user_id', $like->to_user_id)
                ->first();
            // Check notification_setting and send push notification
            if ($notification_setting && $notification_setting->push_notif_like == NotificationSetting::IS_ON) {
                $push_notification_content = PushNotificationContent::where('timing_type', PushNotificationContent::TYPE_LIKE)->first();
                if ($push_notification_content) {
                    $push_notification = new PushNotification();
                    $push_notification->push_notification_content_id = $push_notification_content->id;
                    $push_notification->to_user_id = $like->to_user_id;
                    $push_notification->reservation_time = $today;
                    $push_notification->is_sent = 0;
                    $push_notification->save();
                }
            }
        }
    }

    public function createEmail(Like $like): void
    {
        if ($like) {
            $today = Carbon::now();
            $notification_setting = NotificationSetting::where('user_id', $like->to_user_id)
                ->first();
            // Check notification_setting and send email
            if ($notification_setting && $notification_setting->email_notif_like == NotificationSetting::IS_ON) {
                $email_content = EmailContent::where('timing_type', EmailContent::TYPE_LIKE)
                    ->first();
                if ($email_content) {
                    $email = new Email();
                    $email->email_content_id = $email_content->id;
                    $email->to_user_id = $like->to_user_id;
                    $email->reservation_time = $today;
                    $email->is_sent = 0;
                    $email->save();
                }
            }
        }
    }
}
