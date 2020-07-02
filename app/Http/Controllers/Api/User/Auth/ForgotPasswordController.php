<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Enums\ErrorType;
use App\Enums\DBConstant;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\Auth\ForgotPasswordRequest;
use App\Services\PasswordResetService;
use App\Services\UserAuthService;
use App\Services\UserService;

class ForgotPasswordController extends ApiController
{
    protected $userAuthService;
    protected $userService;
    protected $passwordResetService;

    public function __construct(
        UserAuthService $userAuthService,
        UserService $userService,
        PasswordResetService $passwordResetService
    ) {
        $this->userAuthService = $userAuthService;
        $this->userService = $userService;
        $this->passwordResetService = $passwordResetService;
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $user = $this->userService->getUserByEmail($request->email);
        if (!$user) {
            return $this->sendError(ErrorType::CODE_4040, ErrorType::STATUS_4040, trans('passwords.user'));
        }

        if ($user->is_authenticated == DBConstant::AUTH_UNAUTHENTICATED) {
            return $this->sendError(ErrorType::CODE_4040, ErrorType::STATUS_4040, trans('errors.MSG_4040'));
        }
        
        $passwordResetOld = $this->passwordResetService->getPasswordResetByEmail($user->email);
        if ($passwordResetOld) {
            $this->passwordResetService->delete($passwordResetOld->id);
        }

        $resetToken = $this->passwordResetService->generateResetToken();
        $userType = DBConstant::USER_TYPE_GENERAL_USER;
        $passwordReset = $this->passwordResetService->create($userType, $user->email, $resetToken);

        // send mail
        $this->userAuthService->sendMailResetPassword($user, $resetToken);

        return $this->sendSuccess(null, trans('passwords.sent'));
    }
}
