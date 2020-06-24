<?php

declare(strict_types=1);

namespace App\Repositories\OperatingCompanyUser;

use App\Enums\DBConstant;
use App\Enums\ErrorType;
use App\Mail\OCInviteUserMailContent;
use App\Models\EmailAuthn;
use App\Models\EmailContent;
use App\Models\OperatingCompanyUser;
use App\Repositories\RepositoryAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use JWTAuth;
use App\Enums\Constant;

class OperatingCompanyUserRepository extends RepositoryAbstract implements OperatingCompanyUserRepositoryInterface
{
    /**
     * Construct.
     *
     * @param OperatingCompanyUser     $OperatingCompanyUser
     */
    public function __construct(OperatingCompanyUser $opCompanyUser)
    {
        parent::__construct();
        $this->model = $opCompanyUser;
    }
    /**
     * get oc user list.
     * @author huydn
     * @param $params, $request
     * @param mixed $request
     * @return $data
     */
    public function getOCUserList($params, $request)
    {
        $limit = $params['limit'];
        $current_page = $params['current_page'];
        $orderBy = $params['orderBy'];
        $desc = $params['desc'];
        $data = OperatingCompanyUser::select(
            'operating_company_users.oc_user_id',
            'operating_company_users.authority_type AS authority_type_format',
            'operating_company_users.authority_type',
            'operating_company_users.email',
            'operating_company_users.name'
        )->where('is_archived', DBConstant::ARCHIVE_FLAG_NOT_ARCHIVED);

        $filters = [
            'operating_company_users.oc_user_id' => [
                'where' => '=',
                'value' => null,
            ],
            'operating_company_users.name' => [
                'where' => 'like',
                'value' => null,
            ],
            'operating_company_users.authority_type' => [
                'where' => '=',
                'value' => null,
            ],
            'operating_company_users.email' => [
                'where' => 'like',
                'value' => null,
            ],
        ];
        $filters['operating_company_users.oc_user_id']['value'] = $request->query('oc_user_id') ?? '';
        $filters['operating_company_users.name']['value'] = $request->query('name') ?? '';
        $filters['operating_company_users.authority_type']['value'] = $request->query('authority_type') ?? '';
        $filters['operating_company_users.email']['value'] = $request->query('email') ?? '';

        foreach ($filters as $key => $where) {
            if (!$where['value']) {
                continue;
            }
            if ($where['where'] == 'like') {
                $data = $data->where($key, 'like', '%' . $where['value'] . '%');
            } elseif ($where['where'] == '=') {
                $data = $data->where($key, '=', $where['value']);
            }
        }
        $total = $data->get()->count();
        $data = $data->orderBy($orderBy, $desc)->limit($limit)->offset(($current_page - 1) * $limit);

        return [
            'oc_users' => $data->get(),
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * update oc user.
     * @author huydn
     * @param $data
     */
    public function updateOrCreate($data): void
    {
        if (array_key_exists('oc_user_id', $data)) {
            $attributes = ['oc_user_id' => $data['oc_user_id']];
            OperatingCompanyUser::updateOrCreate($attributes, $data);
        } else {
            OperatingCompanyUser::updateOrCreate($data);
        }
    }

    /**
     * delete oc user.
     * @author huydn
     * @param $id
     * */
    public function delete($id): void
    {
        $user = $this->model->find($id);
        if ($user != null) {
            $user->is_archived = DBConstant::ARCHIVE_FLAG_ARCHIVED;
            $user->save();
        }
    }

    /**
     * Invite OCUser Admin/Editor.
     * @author huydn
     * @param $request
     * @return array
     */
    public function invite($request)
    {
        $user = OperatingCompanyUser::withTrashed()->where('email', '=', $request->email)->first();

        if ($user && $user->is_archived == 1) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4032,
                    'res_status' => ErrorType::STATUS_4032,
                    'msg' => __('errors.CODE_4032'),
                ]
            ];
        }

        if ($user && $user->created_at != $user->updated_at) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4220,
                    'res_status' => ErrorType::STATUS_4220,
                    'msg' => [
                        'email' => [
                            'email_unique'
                        ]
                    ]
                ]
            ];
        }

        if ($user) {
            $emailAuth = EmailAuthn::where('email', '=', $user->email)
                ->where('user_type', '=', DBConstant::EMAIL_OPERATION_USER_TYPE)
                ->orderBy('created_at', 'desc')
                ->whereColumn('updated_at', '=', 'created_at')
                ->first();
            if ($emailAuth && $emailAuth->created_at > now()->subMinute(Constant::MAX_TIME_EXPIRED_SEND_EMAIL)) {
                return [
                    'success' => false,
                    'data' => [
                        'err_code' => ErrorType::CODE_4014,
                        'res_status' => ErrorType::STATUS_4014,
                        'msg' => __('errors.CODE_4014')
                    ]
                ];
            }
        }

        try {
            DB::beginTransaction();
            $authorType = $request->authority_type;
            $emailInvite = $request->email;
            $token = Str::random(60);

            if (!$user) {
                $user = new OperatingCompanyUser();
                $user->authority_type = $authorType;
                $user->email = $emailInvite;
                $user->save();
            }
            
            $emailContent['url'] = env('APP_URL') . '/operation/register-admin?token=' . $token;
            Mail::to($emailInvite)->send(new OCInviteUserMailContent($emailContent));
            
            $emailAuth = new EmailAuthn();
            $emailAuth->user_type = DBConstant::EMAIL_OPERATION_USER_TYPE;
            $emailAuth->email = $emailInvite;
            $emailAuth->token = $token;
            $emailAuth->save();
            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_5002,
                    'msg' => $e->getMessage(),
                    'res_status' => ErrorType::STATUS_5002]];
        }
    }

    /**
     * Update name and password to register.
     * @author huydn
     * @param $request, $token
     * @param mixed $token
     * @return $userRegister
     */
    public function register($request)
    {
        $emailAuth = EmailAuthn::where('email', $request->email);
        if ($emailAuth) {
            $emailAuth->update(['updated_at' => now()]);
        }
        $userRegister = OperatingCompanyUser::where('email', $request->email)->firstOrFail();
        $userRegister->name = $request->name;
        $userRegister->password = bcrypt($request->password);
        $userRegister->save();
        return $userRegister;
    }

    /**
     * @author huydn
     * @return
     */
    public function getProfile()
    {
        $ocUserId = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['oc_user_id'];
        $userProfile = OperatingCompanyUser::where('oc_user_id', $ocUserId)->firstOrFail();

        return $userProfile;
    }

    /**
     * update profile.
     * @author huydn
     * @param $data
     */
    public function updateProfile($data): void
    {
        $ocUserId = JWTAuth::getPayload(JWTAuth::getToken())->toArray()['oc_user_id'];
        $data['password'] = bcrypt($data['password']);
        parent::update($ocUserId, $data);
    }

    /**
     * Check user invite to operation
     */
    public function checkUserInvite($token)
    {
        $emailAuth = EmailAuthn::where('token', '=', $token)->where('user_type', DBConstant::USER_TYPE_OPERATING_COMPANY_USER)->first();
        if (!$emailAuth) return null;

        return OperatingCompanyUser::where('email', '=', $emailAuth->email)->first();
    }
}
