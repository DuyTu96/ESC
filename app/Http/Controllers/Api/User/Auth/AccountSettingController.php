<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Enums\DBConstant;
use App\Http\Controllers\Api\ApiController;
use App\Enums\ErrorType;
use App\Http\Requests\User\Auth\ChangeEmailRequest;
use App\Http\Requests\User\Auth\ChangePasswordRequest;
use App\Http\Requests\User\Auth\SendChangeLinkEmailRequest;
use App\Mail\User\ChangeMail;
use App\Services\EmailAuthenticationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Mail;

class AccountSettingController extends ApiController
{
    private $userService;
    private $emailAuthenticationService;

    public function __construct(
        UserService $userService,
        EmailAuthenticationService $emailAuthenticationService
    ) {
        $this->userService = $userService;
        $this->emailAuthenticationService = $emailAuthenticationService;
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        if ($this->isSocialUser()) {
            return $this->sendError(ErrorType::CODE_4030, ErrorType::STATUS_4030, trans('errors.MSG_4030'));
        }
        $user = $this->getGuard('user')->user();
        $changePass = $this->userService->updatePassword($user->user_id, $request->password);

        return $this->sendSuccess($changePass, trans('response.success'));
    }

    public function sendChangeLinkEmail(SendChangeLinkEmailRequest $request)
    {
        $user = $this->getGuard('user')->user();
        if (!$user) {
            return $this->sendError(ErrorType::CODE_4040, ErrorType::STATUS_4040, trans('errors.MSG_4040'));
        }
        $userId = $user->user_id;
        $emailChange = base64_encode($request->email);
        $emailTime = base64_encode(Date::now());
        $urlParam = '?email=' . $emailChange . '&user=' . $userId . '&time=' . $emailTime;
        $emailContent['title'] = trans('register.title');
        $emailContent['body'] = trans('register.body');
        $emailContent['url'] = url(sprintf(config('auth.confirm_change_mail_url'), $urlParam));
        Mail::to($user->email)->queue(new ChangeMail($emailContent));

        return ['success' => true];
    }

    public function userByIdRequestChangeEmail(Request $request)
    {
        $userId = $request->user;
        if (!$userId) {
            return $this->sendError(ErrorType::CODE_4012, ErrorType::STATUS_4012, trans('errors.MSG_4012'));
        }

        $emailChange = base64_decode($request->email);
        if (!filter_var( $emailChange, FILTER_VALIDATE_EMAIL )) {
            return $this->sendError(ErrorType::CODE_4040, ErrorType::STATUS_4040, trans('errors.MSG_4040'));
        }

        $userUser = $this->userService->getUserById($userId);
        if(!$userUser) {
            return $this->sendError(ErrorType::CODE_4040, ErrorType::STATUS_4040, trans('errors.MSG_4040'));
        }

        $data['user'] = $userUser->user_id;
        $data['email_change'] = base64_encode($emailChange);
        $data['time'] = $request->time;

        return $this->sendSuccess($data, trans('response.success'));
    }

    public function changeEmail(ChangeEmailRequest $request)
    {
        if ($this->isSocialUser()) {
            return $this->sendError(ErrorType::CODE_4030, ErrorType::STATUS_4030, trans('errors.MSG_4030'));
        }
        $user = $this->getGuard('user')->user();
        if (!$user) {
            return $this->sendError(ErrorType::CODE_4042, ErrorType::STATUS_4042, trans('errors.MSG_4042'));
        }
        $email = $request->email;
        $emailTime = base64_decode($request->time);
        $configEmailExpire = config('auth.email_auth_timeout');
        $isEmailExpire = $this->userService->isConfirmEmailExpire($emailTime, $configEmailExpire);
        if ($isEmailExpire) {
            return $this->sendError(ErrorType::CODE_4011, ErrorType::STATUS_4011, trans('errors.MSG_4011'));
        }

        $changeEmail = $this->userService->updateEmail($user->user_id, $email);

        return $this->sendSuccess($changeEmail, trans('response.success'));
    }

}
