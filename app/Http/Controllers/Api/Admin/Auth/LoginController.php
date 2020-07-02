<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Enums\DBConstant;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Common\LoginRequest;
use App\Enums\ErrorType;
use App\Services\CompanyAdminUserService;
use App\Services\EmailAuthenticationService;

class LoginController extends ApiController
{
    private $companyAdminUserService;
    private $emailAuthentication;

    public function __construct(
        CompanyAdminUserService $companyAdminUserService,
        EmailAuthenticationService $emailAuthentication
    ) {
        $this->companyAdminUserService = $companyAdminUserService;
        $this->emailAuthentication = $emailAuthentication;
    }
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'is_authenticated' => DBConstant::AUTH_AUTHENTICATED,
        ];

        $user = $this->companyAdminUserService->getUserByEmail($credentials['email']);
        $emailAuth = $this->emailAuthentication->getEmailAuthByRequest($credentials['email']);
        if ($user && $emailAuth) {
            $tokenExpireTime = config('auth.email_auth_timeout');
            $isResetTokenExpire = $this->emailAuthentication->isConfirmTokenExpire($emailAuth->created_at, $tokenExpireTime);
            if ($isResetTokenExpire) {
                $emailUser = $this->emailAuthentication->delete($emailAuth->id);
                $user = $this->companyAdminUserService->delete($user->company_admin_user_id);

                return $this->sendError(ErrorType::CODE_4010, ErrorType::STATUS_4010, trans('errors.MSG_4010'));
            }
        }

        $rememberMe = isset($request->remember_me) && $request->remember_me;

        if ($rememberMe) {
            $this->getGuard()->setTTL(config('jwt.ttl_remember'));
        }

        if (!$token = $this->getGuard()->attempt($credentials)) {
            return $this->sendError(ErrorType::CODE_4010, ErrorType::STATUS_4010, trans('errors.MSG_4010'));
        }

        return $this->respondWithToken($token, $rememberMe);
    }

    protected function respondWithToken($token, $rememberMe)
    {
        $data = [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->getGuard()->factory()->getTTL() * 60,
            'remember_me' => $rememberMe,
        ];

        return $this->sendSuccess($data, trans('response.success'));
    }

    public function logout()
    {
        $this->getGuard()->logout();

        return $this->sendSuccess(null, trans('response.success'));
    }

    public function getAuthenticatedUser()
    {
        $data = $this->getGuard()->user();
        $user['company_admin_user_id'] = $data['company_admin_user_id'];
        $user['company_id'] = $data['company_id'];
        $user['is_authenticated'] = $data['is_authenticated'];
        $user['email'] = $data['email'];

        return $this->sendSuccess($user, trans('response.success'));
    }
}
