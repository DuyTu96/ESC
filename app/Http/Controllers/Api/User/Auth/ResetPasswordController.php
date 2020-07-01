<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Enums\ErrorType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\Auth\ResetPasswordRequest;
use App\Http\Requests\Common\CheckResetPasswordTokenRequest;
use App\Services\PasswordResetService;
use App\Services\UserService;
use Illuminate\Http\Request;

class ResetPasswordController extends ApiController
{
    protected $userService;
    protected $passwordResetService;

    public function __construct(
        UserService $userService,
        PasswordResetService $passwordResetService
    ) {
        $this->userService = $userService;
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Reset password
     *
     * @param ResetPasswordRequest $request
     * @return void
     */
    public function reset(ResetPasswordRequest $request)
    {
        $token = $request->token;
        $passwordReset = $this->passwordResetService->getPasswordResetByToken($token);
        if (!$passwordReset) {
            return $this->sendError(ErrorType::CODE_4012, ErrorType::STATUS_4012, trans('passwords.token'));
        }

        $tokenExpireTime = config('auth.passwords.users.expire');
        $isResetTokenExpire = $this->passwordResetService->isResetTokenExpire($passwordReset->created_at, $tokenExpireTime);
        if ($isResetTokenExpire) {
            return $this->sendError(ErrorType::CODE_4011, ErrorType::STATUS_4011, trans('passwords.token'));
        }

        $user = $this->userService->getUserByEmail($passwordReset->email);
        if (!$user) {
            return $this->sendError(ErrorType::CODE_4040, ErrorType::STATUS_4040, trans('passwords.user'));
        }

        $user = $this->userService->updatePassword($user->user_id, $request->password);
        $passwordReset = $this->passwordResetService->delete($passwordReset->id);

        return $this->sendSuccess(null, trans('passwords.reset'));
    }

    public function checkToken(CheckResetPasswordTokenRequest $request)
    {
        $token = $request->token;
        $passwordReset = $this->passwordResetService->getPasswordResetByToken($token);
        if (!$passwordReset) {
            return $this->sendError(ErrorType::CODE_4011, ErrorType::STATUS_4011, trans('errors.MSG_4011'));
        }

        $tokenExpireTime = config('auth.passwords.users.expire');
        $isResetTokenExpire = $this->passwordResetService->isResetTokenExpire($passwordReset->created_at, $tokenExpireTime);
        if ($isResetTokenExpire) {
            return $this->sendError(ErrorType::CODE_4011, ErrorType::STATUS_4011, trans('errors.MSG_4011'));
        }

        return $this->sendSuccess(null, trans('response.success'));
    }
}
