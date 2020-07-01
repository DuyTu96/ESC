<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Enums\ErrorType;
use App\Enums\DBConstant;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Admin\Auth\ForgotPasswordRequest;
use App\Services\AdminAuthService;
use App\Services\CompanyAdminUserService;
use App\Services\PasswordResetService;

class ForgotPasswordController extends ApiController
{
    protected $adminAuthService;
    protected $companyAdminUserService;
    protected $passwordResetService;

    public function __construct(
        AdminAuthService $adminAuthService,
        CompanyAdminUserService $companyAdminUserService,
        PasswordResetService $passwordResetService
    ) {
        $this->adminAuthService = $adminAuthService;
        $this->companyAdminUserService = $companyAdminUserService;
        $this->passwordResetService = $passwordResetService;
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $user = $this->companyAdminUserService->getUserByEmail($request->email);
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
        $userType = DBConstant::USER_TYPE_ADMIN_USER;
        $passwordReset = $this->passwordResetService->create($userType, $user->email, $resetToken);

        // send mail
        $this->adminAuthService->sendMailResetPassword($user, $resetToken);

        return $this->sendSuccess(null, trans('passwords.sent'));
    }
}
