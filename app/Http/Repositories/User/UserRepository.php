<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Enums\DBConstant;
use App\Enums\ErrorType;
use App\Models\EmailAuthn;
use App\Models\User;
use App\Repositories\RepositoryAbstract;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository extends RepositoryAbstract implements UserRepositoryInterface
{
    public function __construct(User $userModel)
    {
        parent::__construct();
        $this->model = $userModel;
        $this->table = 'users';
    }

    public function getUserList($params, $request)
    {
        $limit = $params['limit'];
        $current_page = $params['current_page'];
        $orderBy = $params['orderBy'];
        $desc = $params['desc'];
        $filters = [
            'users.user_id' => [
                'where' => '=',
                'value' => null,
            ],
            'users.name' => [
                'where' => 'like',
                'value' => null,
            ],
            'users.nickname' => [
                'where' => 'like',
                'value' => null,
            ],
            'users.email' => [
                'where' => 'like',
                'value' => null,
            ],
            'users.member_type' => [
                'where' => '=',
                'value' => null,
            ],
        ];
        $filters['users.user_id']['value'] = $request->query('user_id') ?? '';
        $filters['users.name']['value'] = $request->query('user_name') ?? '';
        $filters['users.nickname']['value'] = $request->query('user_nickname') ?? '';
        $filters['users.email']['value'] = $request->query('user_email') ?? '';
        $filters['users.member_type']['value'] = $request->query('member_type') ?? '';

        $data = User::select('users.*', 'users.member_type as member_type_title', 'users.point_amount as point_amount_title');

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
            'users' => $data->get(),
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Get user by id.
     * @author fabbi-hoibq
     * @param id
     * @return array
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return [];
        }

        return [
            'user' => $user,
        ];
    }

    public function getUserInfo()
    {
        $id = Auth::user()->id;
        $user = User::select('users.nickname',
            'users.point_amount',
            'users.name',
            'users.member_type',
            'users.email',
            'reservations.reservation_id')
            ->leftJoin('reservations', 'users.user_id', '=', 'reservations.user_id')
            ->where('users.user_id', $id)
            ->get();

        return $user;
    }

    public function getDataEditInfoForm()
    {
        $id = Auth::user()->id;
        $user = User::select('users.name',
            'users.nickname',
            'users.email',
            'users.password',
            'users.email')->where('users.user_id', $id)->get();

        return $user;
    }

    public function getIdFavoriteShopByUser()
    {
        $idAuth = Auth::user()->id;
        $shopId = User::select('favorites.shop_id')
            ->join('favorites', 'users.user_id', '=', 'favorites.user_id')
            ->where('favorites.user_id', $idAuth)->get()->toArray();

        return $shopId;
    }

    /**
     * Member register
     * @param $request
     * @return array
     * */
    public function register($request)
    {
        $data = $request->all();
        $user = [
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'last_login' => Carbon::now(),
            'name' => $request->name ? $data['name'] : '',
            'nickname' => $request->nickname ? $data['nickname'] : '',
        ];
        DB::beginTransaction();
        try {
            $this->store($user);
            $token = Str::random(64);
            EmailAuthn::create([
                'user_type' => DBConstant::USER_TYPE_MEMBER_USER,
                'email' => $data['email'],
                'token' => $token,
            ]);
            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_5004,
                    'msg' => $e->getMessage(),
                    'res_status' => ErrorType::STATUS_5004,
                ]
            ];
        }
    }

    /**
     * Member verify token
     * @param $token
     * @return array
     * */
    public function verifyToken($token)
    {
        $time = Carbon::now()->subMinute(30);
        $token = EmailAuthn::where('token', $token)->whereColumn('created_at', 'updated_at')->where('created_at', '>', $time)->first();
        if ($token == null) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4012,
                    'msg' => __('errors.MSG_4012'),
                    'res_status' => ErrorType::STATUS_4012
                ]
            ];
        }
        return ['success' => true];
    }
}
