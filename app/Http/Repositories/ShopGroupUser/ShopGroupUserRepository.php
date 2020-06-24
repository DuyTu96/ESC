<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroupUser;

use App\Enums\Constant;
use App\Enums\DBConstant;
use App\Enums\ErrorType;
use App\Mail\OCInviteShopGroupAdminMailContent;
use App\Mail\SGInviteUserMailContent;
use App\Models\EmailAuthn;
use App\Models\EmailContent;
use App\Models\ShopGroup;
use App\Models\ShopGroupUser;
use App\Models\ShopGroupUserMap;
use App\Repositories\RepositoryAbstract;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ShopGroupUserRepository extends RepositoryAbstract implements ShopGroupUserRepositoryInterface
{
    protected $shopGroupUser;
    protected $emailAuthModel;

    public function __construct(ShopGroupUser $shopGroupUser, EmailAuthn $emailAuthModel)
    {
        parent::__construct();
        $this->model = $shopGroupUser;
        $this->emailAuthModel = $emailAuthModel;
        $this->table = 'shop_group_users';
    }

    /**
     * Get list shop_group_users.
     * @param $request
     * @return array
     */
    public function getListOfCurrentUser($request)
    {
        $limit = ($request->limit) ? $request->limit : Constant::BASIC_LIMIT;
        $currentPage = ($request->current_page) ? $request->current_page : 1;
        $sortField = ($request->sortBy) ? $request->sortBy : 'name';
        $sortType = ($request->isDescending === 'DESC') ? 'DESC' : 'ASC';
        $shopGroupId = $request->user()->shop_group_id;

        $shop = ShopGroupUser::select(
            'shop_group_users.*',
            'shop_group_users.authority_type as authority_type_format',
            DB::raw(Constant::SHOP_GROUP_USER_ALL_SHOP_NAME . ' AS shop_name, null AS shop_id')
        )->where('shop_group_users.authority_type', DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR)
            ->where('shop_group_users.shop_group_id', $shopGroupId);

        $data = ShopGroupUser::select(
            'shop_group_users.*',
            'shop_group_users.authority_type as authority_type_format',
            'shops.shop_name_ja as shop_name',
            'shops.shop_id'
        )->join('shop_group_user_maps', 'shop_group_users.sg_user_id', '=', 'shop_group_user_maps.sg_user_id')
            ->join('shops', 'shop_group_user_maps.shop_id', '=', 'shops.shop_id')
            ->where('shop_group_users.authority_type', DBConstant::SHOP_GROUP_USER_SHOP_MANAGER)
            ->where('shop_group_users.shop_group_id', $shopGroupId);

        $data = $data->unionAll($shop);
        $total = $data->get()->count();
        $shopGroupUsers = $data->limit($limit)->offset(($currentPage - 1) * $limit)->orderBy($sortField, $sortType)->get();

        return [
            'users' => $shopGroupUsers,
            'pagination' => [
                'total' => $total,
                'current_page' => $currentPage,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Update or create new shop_group_user.
     * @param $request
     * @return array
     * */
    public function updateOrCreate($request)
    {
        $formData = $request->all();
        $isEdit = array_key_exists('sg_user_id', $formData);
        $isAdmin = $formData['authority_type'] == DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR;

        DB::beginTransaction();

        try {
            if ($isEdit) {
                // Find user
                $shopGroupUser = ShopGroupUser::find($formData['sg_user_id']);
                $oldAuthorityType = $shopGroupUser->authority_type;
                if ($shopGroupUser == null) {
                    DB::rollBack();

                    return [
                        'success' => false,
                        'data' => [
                            'err_code' => ErrorType::CODE_4040,
                            'msg' => __('errors.MSG_4040'),
                            'res_status' => ErrorType::STATUS_4040,
                        ],
                    ];
                }

                // Update user
                $shopGroupUser->update($formData);
                if ($oldAuthorityType == $formData['authority_type'] && $isAdmin) {
                    DB::commit();
                    // User is shop admin, just update information
                    return ['success' => true];
                }

                if ($oldAuthorityType != $formData['authority_type'] && !$isAdmin) {
                    ShopGroupUserMap::create(['shop_id' => $formData['shop_id'], 'sg_user_id' => $formData['sg_user_id']]);
                    DB::commit();
                    // User is shop manager, before is admin: just create shop group user map
                    return ['success' => true];
                }

                $shopGroupUserMap = ShopGroupUserMap::where('sg_user_id', $formData['sg_user_id'])->first();
                if ($shopGroupUserMap == null) {
                    DB::rollBack();

                    return [
                        'success' => false,
                        'data' => [
                            'err_code' => ErrorType::CODE_4040,
                            'msg' => __('errors.MSG_4040'),
                            'res_status' => ErrorType::STATUS_4040,
                        ],
                    ];
                }

                if ($oldAuthorityType == $formData['authority_type'] && !$isAdmin) {
                    // User is shop manager, just update shop group user map
                    $shopGroupUserMap->update(['shop_id' => $formData['shop_id']]);
                } else {
                    // User is shop admin, before is manager: just delete shop group user map
                    $shopGroupUserMap->delete();
                }
            } else {
                // Create user
                $name = $formData['shop_name'];
                $formData['password'] = Str::random(20);
                $formData['shop_group_id'] = $request->user()->shop_group_id;
                $shopGroupUser = ShopGroupUser::create($formData);
                if (!$isAdmin) {
                    // Create shop group user map
                    ShopGroupUserMap::create(['shop_id' => $formData['shop_id'], 'sg_user_id' => $shopGroupUser->sg_user_id]);
                }

                // Send mail
                $token = Str::random(60);
                $emailContent['name'] = $name;
                $emailContent['url'] = env('APP_URL') . '/shop/register?token=' . $token;
                Mail::to($shopGroupUser->email)->send(new SGInviteUserMailContent($emailContent));

                // Create token
                EmailAuthn::create([
                    'user_type' => DBConstant::USER_TYPE_SHOP_GROUP_USER,
                    'email' => $shopGroupUser->email,
                    'token' => $token,
                ]);
            }
            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_5002,
                    'msg' => $e->getMessage(),
                    'res_status' => ErrorType::STATUS_5002,
                ],
            ];
        }
    }

    /**
     * Delete shop_group_user.
     * @param $id
     * */
    public function delete($id): void
    {
        $user = ShopGroupUser::find($id);
        if ($user) {
            $user->delete();
        }
    }

    /**
     * Get token data.
     * @author huydn
     * @param $token
     * @return ShopGroupUser
     */
    public function getTokenData($token)
    {
        $tokenData = EmailAuthn::select('shop_group_users.authority_type', 'email_authns.email')
            ->join('shop_group_users', 'shop_group_users.email', 'email_authns.email')
            ->where('email_authns.token', $token)
            ->whereColumn('email_authns.created_at', 'email_authns.updated_at')
            ->where('email_authns.user_type', DBConstant::USER_TYPE_SHOP_GROUP_USER)
            ->first();

        return $tokenData;
    }

    /**
     * Add shop group and sign up shop group admin.
     * @author huydn
     * @param $request
     * @return array
     */
    public function register($request)
    {
        $formData = $request->all();
        $tokenData = $this->getTokenData($formData['token']);
        if ($tokenData == null) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4013,
                    'msg' => __('errors.MSG_4013'),
                    'res_status' => ErrorType::STATUS_4013,
                ],
            ];
        }
        $formData['shop_group_user']['password'] = bcrypt($formData['shop_group_user']['password']);

        DB::beginTransaction();

        try {
            $shopGroupUser = ShopGroupUser::where('email', $tokenData->email)->first();
            if ($shopGroupUser == null) {
                DB::rollBack();

                return [
                    'success' => false,
                    'data' => [
                        'err_code' => ErrorType::CODE_4040,
                        'msg' => __('errors.MSG_4040'),
                        'res_status' => ErrorType::STATUS_4040,
                    ],
                ];
            }

            $shopGroupUser->update($formData['shop_group_user']);

            if ($tokenData->authority_type == DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR) {
                $shopGroup = new ShopGroup();
                $shopGroup->name = $formData['shop_group']['name'] ?? '';
                $shopGroup->ceo_name = $formData['shop_group']['ceo_name'] ?? '';
                $shopGroup->staff_name = $formData['shop_group']['staff_name'] ?? '';
                $shopGroup->phone = $formData['shop_group']['phone'] ?? '';
                $shopGroup->address = $formData['shop_group']['address'] ?? '';
                $shopGroup->save();
                $shopGroupUser->shop_group_id = $shopGroup->shop_group_id;
                $shopGroupUser->save();
            }

            EmailAuthn::where('token', $formData['token'])->first()->touch();

            DB::commit();

            return ['success' => true];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_5002,
                    'msg' => $e->getMessage(),
                    'res_status' => ErrorType::STATUS_5002,
                ],
            ];
        }
    }

    /**
     * Update shop_group_user.
     *
     * @param $request
     * @return array
     * */
    public function updateShopGroupUser($request)
    {
        $data = $request->all();
        $this->model->find($request->user->sg_user_id)->update([
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Get shop group detail.
     *
     * @author huydn
     * @return array
     */
    public function getShopGroupDetail()
    {
        $shopGroupId = Auth::user()->shop_group_id;
        $shopGroupDetail = ShopGroup::where('shop_group_id', $shopGroupId)->firstOrFail();

        return $shopGroupDetail;
    }

    /**
     * Get user detail.
     *
     * @author LinhNT
     * @param $request
     * @return array
     */
    public function getUser($request)
    {
        $user = ShopGroupUser::find($request->user()->sg_user_id);
        if ($user == null) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4041,
                    'msg' => __('errors.MSG_4041'),
                    'res_status' => ErrorType::STATUS_4041,
                ],
            ];
        }
        $shopGroupInfo = null;
        if ($user->authority_type == DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR) {
            $shopGroupInfo = ShopGroup::find($user->shop_group_id);
        }

        return ['success' => true, 'data' => ['user' => $user, 'shop_group_info' => $shopGroupInfo]];
    }

    public function updateProfile($request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();
            $authorityType = $request->user()->authority_type;
            $user = $this->model->find($request->user()->sg_user_id);
            if ($user == null) {
                DB::rollBack();

                return [
                    'success' => false,
                    'data' => [
                        'err_code' => ErrorType::CODE_4041,
                        'msg' => __('errors.MSG_4041'),
                        'res_status' => ErrorType::STATUS_4041,
                    ],
                ];
            }

            $user->update([
                'name' => $data['user']['name'],
                'password' => Hash::make($data['user']['password']),
            ]);
            if ($authorityType == 1) {
                $shopGroup = ShopGroup::find($request->user()->shop_group_id);
                if ($shopGroup) {
                    $shopGroup->update($data['shop_group_info']);
                }
            }
            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_5002,
                    'msg' => __('errors.MSG_5002'),
                    'res_status' => ErrorType::STATUS_5002,
                ],
            ];
        }
    }

    /**
     * Invite shop group.
     * @param mixed $data
     * @return array
     */
    public function inviteShopGroup($data)
    {
        $user = $this->model::withTrashed()->where('email', '=', $data['email'])->first();

        if ($user && $user->is_archived == 1) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4033,
                    'res_status' => ErrorType::CODE_4033,
                    'msg' => __('errors.CODE_4033')
                ]
            ];
        }

        // User invited and complete register
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
                ->where('user_type', '=', DBConstant::EMAIL_SHOP_GROUP_USER_TYPE)->orderBy('created_at', 'desc')->whereColumn('updated_at', '=', 'created_at')->first();
            if ($emailAuth && $emailAuth->created_at > now()->subMinute(Constant::MAX_TIME_EXPIRED_SEND_EMAIL)) {
                return [
                    'success' => false,
                    'data' => [
                        'err_code' => ErrorType::CODE_4014,
                        'res_status' => ErrorType::CODE_4014,
                        'msg' => __('errors.CODE_4014')
                    ]
                ];
            }
        }
        try {
            DB::beginTransaction();
            // If user has not already invite before
            if (!$user) {
                $dataShopGroupUser = [
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(12)),
                ];
                if (isset($data['authority_type'])) {
                    $dataShopGroupUser['authority_type'] = $data['authority_type'];
                } else {
                    $data['authority_type'] = DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR;
                }
                $userShop = $this->model->create($dataShopGroupUser);
                
                if (isset($data['shop_id']) && isset($data['authority_type'])) {
                    ShopGroupUserMap::create(['shop_id' => $data['shop_id'], 'sg_user_id' => $userShop->sg_user_id]);
                }
            }
            $dataEmailAuthn = [
                'user_type' => DBConstant::USER_TYPE_SHOP_GROUP_USER,
                'email' => $data['email'],
                'token' => Str::random(60),
            ];

            $this->emailAuthModel->create($dataEmailAuthn);

            $emailContent['url'] = env('APP_URL') . '/shop/register?token=' . $dataEmailAuthn['token'];

            Mail::to($data['email'])->send(new OCInviteShopGroupAdminMailContent($emailContent));
            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_5002,
                    'message' => $e->getMessage(),
                    'res_status' => ErrorType::STATUS_5002
                ]
            ];
        }
    }
}
