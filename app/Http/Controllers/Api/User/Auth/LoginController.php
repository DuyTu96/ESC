<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Enums\DBConstant;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Common\LoginRequest;
use App\Enums\ErrorType;
use Illuminate\Support\Facades\Crypt;
use App\Services\EmailAuthenticationService;
use App\Services\UserService;
use App\Services\QRCodeService;
use App\Services\BusinessCardService;

class LoginController extends ApiController
{
    private $userService;
    private $emailAuthentication;
    private $qrCodeService;
    private $businessCardService;

    public function __construct(
        UserService $userService,
        EmailAuthenticationService $emailAuthentication,
        QRCodeService $qrCodeService,
        BusinessCardService $businessCardService
    ) {
        $this->userService = $userService;
        $this->emailAuthentication = $emailAuthentication;
        $this->qrCodeService = $qrCodeService;
        $this->businessCardService = $businessCardService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'is_authenticated' => DBConstant::AUTH_AUTHENTICATED,
        ];
        $user = $this->userService->getUserByEmail($credentials['email']);
        $emailAuth = $this->emailAuthentication->getEmailAuthByRequest($credentials['email']);
        if ($user && $emailAuth) {
            $tokenExpireTime = config('auth.email_auth_timeout');
            $isResetTokenExpire = $this->emailAuthentication->isConfirmTokenExpire($emailAuth->created_at, $tokenExpireTime);
            if ($isResetTokenExpire) {
                $user = $this->userService->delete($user->user_id);
                $emailUser = $this->emailAuthentication->delete($emailAuth->id);

                return $this->sendError(ErrorType::CODE_4010, ErrorType::STATUS_4010, trans('errors.MSG_4010'));
            }
        }

        $rememberMe = isset($request->remember_me) && $request->remember_me;

        if ($rememberMe) {
            $this->getGuard()->setTTL(config('jwt.ttl_remember'));
        }

        if (!$token = $this->getGuard('user')->attempt($credentials)) {
            return $this->sendError(ErrorType::CODE_4010, ErrorType::STATUS_4010, trans('errors.MSG_4010'));
        }


        // sync temporary user data
        $sessionId = $request->cookies->get(config('session.cookie'));
        $sessionId = ($sessionId) ? Crypt::decrypt($sessionId, false) : $sessionId;
        $userId = $this->getGuard('user')->user()->user_id;
        $syncData = $this->qrCodeService->inheritTemporaryUserData($userId, $sessionId);

        return $this->respondWithToken($token, $rememberMe);
    }

    protected function respondWithToken($token, $rememberMe)
    {
        $data = [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->getGuard('user')->factory()->getTTL() * 60,
            'remember_me' => $rememberMe,
        ];

        return $this->sendSuccess($data, trans('response.success'));
    }

    public function logout()
    {
        $this->getGuard('user')->logout();

        return $this->sendSuccess(null, trans('response.success'));
    }

    public function getAuthenticatedUser()
    {
        $data = $this->getGuard('user')->user();
        $businessCard = $data->businessCards;
        $isHasBusinessCard = (!$businessCard->isEmpty());

        if ($isHasBusinessCard) {
            $qrCodeIndividual = $this->qrCodeService->generateQRIndividual($data['qr_i_token']);
            $data['qr_code_individual'] = $qrCodeIndividual;
            $data['business_card_id'] = $data->businessCards[0]['business_card_id'];

            $isUserHaveBusinessCardGroup = $this->businessCardService->isUserHaveBusinessCardGroup($data->user_id);
            if ($isUserHaveBusinessCardGroup) {
                $qrCodeGroup = $this->qrCodeService->generateQRGroup($data['qr_g_token']);
                $data['qr_code_group'] = $qrCodeGroup;
            } else {
                $data['qr_g_token'] = null;
            }
        }
        $data = $data->only(['user_id', 'is_authenticated', 'qr_i_token', 'qr_g_token', 'qr_code_individual', 'qr_code_group', 'business_card_id', 'email']);
        $data['is_has_business_card'] = $isHasBusinessCard;
        $data['is_social_user'] = $this->isSocialUser();

        return $this->sendSuccess($data, trans('response.success'));
    }
}
