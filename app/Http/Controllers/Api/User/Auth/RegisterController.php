<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Enums\DBConstant;
use App\Enums\ErrorType;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Controllers\Api\ApiController;
use App\Services\EmailAuthenticationService;
use App\Services\UserAuthService;
use App\Services\UserService;
use Illuminate\Http\Request;

class RegisterController extends ApiController
{
    private $userService;
    private $emailAuthentication;
    private $userAuthService;

    public function __construct(
        UserService $userService,
        EmailAuthenticationService $emailAuthentication,
        UserAuthService $userAuthService
    ) {
        $this->userService = $userService;
        $this->emailAuthentication = $emailAuthentication;
        $this->userAuthService = $userAuthService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        $user = $this->userService->getUserByEmail($data['email']);
        $emailAuth = $this->emailAuthentication->getEmailAuthByRequest($data['email']);
        if ($user) {
            if ($user->is_authenticated == DBConstant::AUTH_AUTHENTICATED) {
                return $this->sendError(ErrorType::CODE_4091, ErrorType::STATUS_4091, trans('errors.MSG_4091'));
            } else {
                $tokenExpireTime = config('auth.email_auth_timeout');
                $isResetTokenExpire = $this->emailAuthentication->isConfirmTokenExpire($user->created_at, $tokenExpireTime);
                if ($isResetTokenExpire) {
                    $user = $this->userService->delete($user->user_id);
                    $emailUser = $this->emailAuthentication->delete($emailAuth->id);
                    $register = $this->userAuthService->register($data);

                    return $this->sendSuccess(true, trans('response.success'));
                } else {
                    return $this->sendError(ErrorType::CODE_4091, ErrorType::STATUS_4091, trans('errors.MSG_4091'));
                }
            }
        } else {
            $register = $this->userAuthService->register($data);
            if ($register['success']) {

                return $this->sendSuccess(true, trans('response.success'));
            }
        }
    }

    public function confirm(Request $request)
    {
        $token = $request->token;
        if (!$token) {
            return $this->sendError(ErrorType::CODE_4012, ErrorType::STATUS_4012, trans('errors.MSG_4012'));
        }
        $emailConfirm = $this->emailAuthentication->getEmailAuthByToken($token);
        if (!$emailConfirm) {
            return $this->sendError(ErrorType::CODE_4041, ErrorType::STATUS_4041, trans('errors.MSG_4041'));
        }
        $email = $emailConfirm->email;
        $user = $this->userService->getUserByEmail($email);
        if(!$user) {
            return $this->sendError(ErrorType::CODE_4040, ErrorType::STATUS_4040, trans('errors.MSG_4040'));
        }
        $tokenExpireTime = config('auth.email_auth_timeout');
        $isResetTokenExpire = $this->emailAuthentication->isConfirmTokenExpire($emailConfirm->created_at, $tokenExpireTime);
        if ($isResetTokenExpire) {
            $deleteMail = $this->emailAuthentication->delete($emailConfirm->id);
            $deleteCompanyAdminUser = $this->userService->delete($user->user_id);

            return $this->sendError(ErrorType::CODE_4011, ErrorType::STATUS_4011, trans('errors.MSG_4011'));
        }
        $activeUser = $this->userService->updateIsAuthenticated($user->user_id);
        $emailUser = $this->emailAuthentication->delete($emailConfirm->id);

        return $this->sendSuccess(true, trans('response.success'));
    }
}
