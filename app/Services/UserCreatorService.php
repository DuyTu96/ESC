<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LoginBonusHistory;
use App\Models\NotificationSetting;
use App\Models\RegularBonusHistory;
use App\Models\User;
use Carbon\Carbon;

class UserCreatorService
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Register a few settings after a user is created.
     *
     * @param User $user
     */
    public function afterCreate(User $user): void
    {
        // Create a regular bonus history record
        $regularBonusHistory = RegularBonusHistory::firstOrNew([
            'user_id' => $user->user_id,
            'given_hearts_amount' => 0,
            'given_date' => Carbon::now(),
            'display_status' => 1,
        ]);
        $regularBonusHistory->save();

        // Create a notification setting record
        $notification_setting = NotificationSetting::where('user_id', $user->user_id)->first();
        if (empty($notification_setting)) {
            $notification_setting = new NotificationSetting();
            $notification_setting->user_id = $user->user_id;
            $notification_setting->push_notif_like = NotificationSetting::IS_ON;
            $notification_setting->push_notif_matched = NotificationSetting::IS_ON;
            $notification_setting->push_notif_message = NotificationSetting::IS_ON;
            $notification_setting->push_notif_staff = NotificationSetting::IS_ON;
            $notification_setting->email_notif_like = NotificationSetting::IS_ON;
            $notification_setting->email_notif_matched = NotificationSetting::IS_ON;
            $notification_setting->email_notif_message = NotificationSetting::IS_ON;
            $notification_setting->email_notif_staff = NotificationSetting::IS_ON;
            $notification_setting->save();
        }

        // Create user node in firebase
        $this->firebaseService->createUserNode($user);
    }

    public function createLoginHistoryBonus(User $user)
    {
        // Create login history check if today is not record will create new
        $today = Carbon::now();
        $yesterday = Carbon::now()->subDays(1);
        $login_history = LoginBonusHistory::where('user_id', $user->user_id)
            ->where('given_date', $today->format('Y-m-d'))
            ->first();
        if (empty($login_history)) {
            $day_x = 1;
            $heart_number = 1;
            // Check if last login is yesterday will increase heart_number to 3 for 3 days login continues
            $last_login = LoginBonusHistory::where('user_id', $user->user_id)
                ->where('given_date', $yesterday->format('Y-m-d'))
                ->first();
            if (!empty($last_login)) {
                if ($last_login->day_x != 3) {
                    $day_x = $last_login->day_x + 1;
                } else {
                    $day_x = 1;
                    $heart_number = 3;
                }
            }

            $login_history = LoginBonusHistory::create([
                'user_id' => $user->user_id,
                'given_hearts_amount' => $heart_number,
                'given_date' => $today->format('Y-m-d'),
                'day_x' => $day_x,
            ]);

            // Add add heart for user
            if (!empty($login_history)) {
                $user->hearts_amount += $heart_number;
                $user->save();
            }
        }

        return $login_history;
    }

    public function getRegularStatus(User $user)
    {
        $regular_status = 0;
        if (!empty($user)) {
            $today = Carbon::now();
            // Get display_status of regular_bonus_history
            $regular_history = RegularBonusHistory::where('user_id', $user->user_id)
                ->where('given_date', $today->format('Y-m-d'))
                ->first();
            if ($regular_history) {
                if ($regular_history->display_status == RegularBonusHistory::STATUS_NOT_SHOW) {
                    $regular_status = 1;
                    $regular_history->display_status = RegularBonusHistory::STATUS_SHOWED;
                    $regular_history->save();
                }
            }
        }

        return $regular_status;
    }
}
