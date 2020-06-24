<?php

declare(strict_types=1);

namespace App\Repositories\Agency;

use App\Models\AgencyUser;

class AgencyRepository implements AgencyRepositoryInterface
{
    /**
     * Get agency user List.
     * @author huydn
     * @param $request, $params
     * @param mixed $params
     * @return $data
     */
    public function getAgencyList($params, $request)
    {
        $limit = $params['limit'];
        $current_page = $params['current_page'];
        $orderBy = $params['orderBy'];
        $desc = $params['desc'];
        $data = AgencyUser::select(
            'agency_users.agcy_user_id',
            'agency_users.agcy_type AS agcy_type_format',
            'agency_users.agcy_type',
            'agency_users.name',
            'agency_users.phone',
            'agency_users.email',
            'agency_users.address'
        );

        $filters = [
            'agency_users.agcy_user_id' => [
                'where' => '=',
                'value' => null,
            ],
            'agency_users.name' => [
                'where' => 'like',
                'value' => null,
            ],
            'agency_users.phone' => [
                'where' => 'like',
                'value' => null,
            ],
            'agency_users.email' => [
                'where' => 'like',
                'value' => null,
            ],
            'agency_users.agcy_type' => [
                'where' => '=',
                'value' => null,
            ],
            'agency_users.address' => [
                'where' => 'like',
                'value' => null,
            ],
        ];
        $filters['agency_users.agcy_user_id']['value'] = $request->query('agcy_user_id') ?? '';
        $filters['agency_users.name']['value'] = $request->query('name') ?? '';
        $filters['agency_users.phone']['value'] = $request->query('phone') ?? '';
        $filters['agency_users.email']['value'] = $request->query('email') ?? '';
        $filters['agency_users.agcy_type']['value'] = $request->query('agcy_type') ?? '';
        $filters['agency_users.address']['value'] = $request->query('address') ?? '';

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
            'agencies' => $data->get(),
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Create or Update agency user.
     * @author huydn
     * @param  $data
     */
    public function updateOrCreate($data): void
    {
        if (array_key_exists('agcy_user_id', $data)) {
            $attributes = ['agcy_user_id' => $data['agcy_user_id']];
            AgencyUser::updateOrCreate($attributes, $data);
        } else {
            AgencyUser::updateOrCreate($data);
        }
    }
}
