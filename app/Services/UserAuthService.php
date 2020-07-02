<?php

declare(strict_types=1);

namespace App\Services;


use App\Enums\DBConstant;
use App\Mail\User\ForgotPasswordMail;
use App\Mail\User\RegisterConfirmEmail;
use App\Models\User;
use App\Services\UserService;
use App\Services\EmailAuthenticationService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserAuthService
{
    protected $userService;
    protected $emailAuthenticationService;

    public function __construct(
        UserService $userService,
        EmailAuthenticationService $emailAuthenticationService
    ) {
        $this->userService = $userService;
        $this->emailAuthenticationService = $emailAuthenticationService;
    }

    public function register($data)
    {
        DB::beginTransaction();
        try {
            $user['is_authenticated'] = DBConstant::AUTH_UNAUTHENTICATED;
            $user['is_archived'] = DBConstant::NOT_ARCHIVED_FLAG;
            $user['email'] = $data['email'];
            $user['password'] = Hash::make($data['password']);
            $user['qr_i_token'] = Str::random(36);
            $user['qr_g_token'] = Str::random(44);
            $userRegister = $this->userService->create($user);

            $token = Str::random(64);
            $emailAuth['user_type'] = DBConstant::USER_TYPE_GENERAL_USER;
            $emailAuth['email'] = $data['email'];
            $emailAuth['token'] = $token;
            $emailAuthentication = $this->emailAuthenticationService->create($emailAuth);

            $emailContent['title'] = trans('register.title');
            $emailContent['body'] = trans('register.body');
            $emailContent['url'] = url(sprintf(config('auth.register_confirm_url'). $token));
            $tokenExpireTime = config('auth.email_auth_timeout');
            Mail::to($data['email'])->queue(new RegisterConfirmEmail($emailContent, $tokenExpireTime));

            DB::commit();

            return ['success' => true];

        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function sendMailResetPassword(User $user, $resetToken)
    {
        $url = url(sprintf(config('auth.password_reset_url'), $resetToken));
        $tokenExpireTime = config('auth.passwords.users.expire');

        return Mail::to($user->email)->queue(new ForgotPasswordMail($url, $tokenExpireTime));

    }
}
