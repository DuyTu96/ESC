<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Enums\DBConstant;
use App\Enums\ErrorType;
use App\Http\Requests\Admin\Auth\RegisterRequest;
use App\Http\Controllers\Api\ApiController;
use App\Services\CompanyAdminUserService;
use App\Services\EmailAuthenticationService;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;

class RegisterController extends ApiController
{
    private $companyAdminUserService;
    private $emailAuthentication;
    private $adminAuthService;

    public function __construct(
        CompanyAdminUserService $companyAdminUserService,
        EmailAuthenticationService $emailAuthentication,
        AdminAuthService $adminAuthService
    ) {
        $this->companyAdminUserService = $companyAdminUserService;
        $this->emailAuthentication = $emailAuthentication;
        $this->adminAuthService = $adminAuthService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        $user = $this->companyAdminUserService->getUserByEmail($data['email']);
        $emailAuth = $this->emailAuthentication->getEmailAuthByRequest($data['email']);
        if ($user) {
            if ($user->is_authenticated == DBConstant::AUTH_AUTHENTICATED) {
                return $this->sendError(ErrorType::CODE_4091, ErrorType::STATUS_4091, trans('errors.MSG_4091'));
            } else {
                $tokenExpireTime = config('auth.email_auth_timeout');
                $isResetTokenExpire = $this->emailAuthentication->isConfirmTokenExpire($user->created_at, $tokenExpireTime);
                if ($isResetTokenExpire) {
                    $user = $this->companyAdminUserService->delete($user->company_admin_user_id);
                    $emailUser = $this->emailAuthentication->delete($emailAuth->id);
                    $register = $this->adminAuthService->register($data);

                    return $this->sendSuccess(true, trans('response.success'));
                } else {
                    return $this->sendError(ErrorType::CODE_4091, ErrorType::STATUS_4091, trans('errors.MSG_4091'));
                }
            }
        } else {
            $register = $this->adminAuthService->register($data);
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
        $user = $this->companyAdminUserService->getUserByEmail($email);
        if(!$user) {
            return $this->sendError(ErrorType::CODE_4040, ErrorType::STATUS_4040, trans('errors.MSG_4040'));
        }
        $tokenExpireTime = config('auth.email_auth_timeout');
        $isResetTokenExpire = $this->emailAuthentication->isConfirmTokenExpire($emailConfirm->created_at, $tokenExpireTime);
        if ($isResetTokenExpire) {
            $deleteMail = $this->emailAuthentication->delete($emailConfirm->id);
            $deleteCompanyAdminUser = $this->companyAdminUserService->delete($user->company_admin_user_id);

            return $this->sendError(ErrorType::CODE_4011, ErrorType::STATUS_4011, trans('errors.MSG_4011'));
        }
        $activeUser = $this->companyAdminUserService->updateIsAuthenticated($user->company_admin_user_id);
        $emailUser = $this->emailAuthentication->delete($emailConfirm->id);

        return $this->sendSuccess(true, trans('response.success'));
    }

}
