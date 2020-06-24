<?php

declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Enums\Constant;
use App\Models\Payment;
use App\Repositories\RepositoryAbstract;

class PaymentRepository extends RepositoryAbstract implements PaymentRepositoryInterface
{
    public function __construct(Payment $payment)
    {
        parent::__construct();
        $this->model = $payment;
        $this->table = 'payments';
    }

    /**
     * Get payment list.
     * @param $request
     * @return array
     * */
    public function getList($request)
    {
        $limit = $request->query('limit') ?? Constant::BASIC_LIMIT;
        $currentPage = $request->query('current_page') ?? 1;
        $sortField = $request->query('sortBy') ?? 'shop_id';
        $sortType = $request->query('isDescending') === 'DESC' ? 'DESC' : 'ASC';
        $query = Payment::select(
            'payments.id AS payment_id',
            'payments.closing_date as closing_date_format',
            'payments.payment_date as payment_date_format',
            'payments.is_paid',
            'payments.from_oc_to_shop',
            'shops.shop_id AS shop_id',
            'shops.shop_name_ja',
            'shop_groups.shop_group_id',
            'shop_groups.name AS shop_group_name'
        )->join('shops', 'shops.shop_id', '=', 'payments.shop_id')
            ->join('shop_groups', 'shop_groups.shop_group_id', '=', 'shops.shop_group_id')
            ->orderBy($sortField, $sortType);

        $searchKeys = [
            'shop_id' => ['column' => 'shops.shop_id', 'operator' => '='],
            'shop_name_ja' => ['column' => 'shops.shop_name_ja', 'operator' => 'like'],
            'shop_group_id' => ['column' => 'shop_groups.shop_group_id', 'operator' => '='],
            'shop_group_name' => ['column' => 'shop_groups.name', 'operator' => 'like'],
            'closing_date' => ['column' => 'closing_date', 'operator' => '='],
            'payment_date' => ['column' => 'payment_date', 'operator' => '='],
            'is_paid' => ['column' => 'is_paid', 'operator' => '='],
        ];

        foreach ($searchKeys as $searchField => $searchColumn) {
            $searchValue = $request->query($searchField);
            if ((string) $searchValue != '' && strlen(trim($searchValue)) > 0) {
                if ($searchColumn['operator'] == 'like') {
                    $query = $query->where($searchColumn['column'], $searchColumn['operator'], '%' . $searchValue . '%');
                } else {
                    $query = $query->where($searchColumn['column'], $searchColumn['operator'], $searchValue);
                }
            }
        }

        $total = $query->count();

        return [
            'payments' => $query->limit($limit)->offset(($currentPage - 1) * $limit)->get(),
            'pagination' => [
                'total' => $total,
                'currentPage' => $currentPage,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Get shop sale report list.
     * @param $limit, $current_page, $orderBy, $desc, $fliterParams
     * @param mixed $current_page
     * @param mixed $orderBy
     * @param mixed $desc
     * @param mixed $fliterParams
     * @return array
     */
    public function getShopSaleReportList($limit, $current_page, $orderBy, $desc, $fliterParams)
    {
        $saleReport = Payment::select(
            'shops.shop_id',
            'shops.shop_name_ja',
            'shop_groups.shop_group_id',
            'shop_groups.name as shop_group_name',
            'payments.closing_date as payment_closing_date',
            'payments.payment_date as payment_payment_date',
            'payments.is_paid as payment_is_paid',
            'payments.from_oc_to_shop as payment_from_oc_to_shop',
        )
            ->join('shops', 'shops.shop_id', '=', 'payments.shop_id')
            ->join('shop_groups', 'shop_groups.shop_group_id', '=', 'shops.shop_group_id');

        $filters = [
            'shops.shop_id' => [
                'where' => '=',
                'value' => null,
            ],
            'shops.shop_name_ja' => [
                'where' => 'like',
                'value' => null,
            ],
            'shop_groups.shop_group_id' => [
                'where' => '=',
                'value' => null,
            ],
            'shop_groups.name' => [
                'where' => 'like',
                'value' => null,
            ],
        ];
        $filters['shops.shop_id']['value'] = $fliterParams['shop_id'];
        $filters['shops.shop_name_ja']['value'] = $fliterParams['shop_name_ja'];
        $filters['shop_groups.shop_group_id']['value'] = $fliterParams['shop_group_id'];
        $filters['shop_groups.name']['value'] = $fliterParams['name'];

        foreach ($filters as $key => $where) {
            if (!$where['value']) {
                continue;
            }
            if ($where['where'] == 'like') {
                $saleReport = $saleReport->where($key, 'like', '%' . $where['value'] . '%');
            } elseif ($where['where'] == '=') {
                $saleReport = $saleReport->where($key, '=', $where['value']);
            }
        }

        $total = $saleReport->count();
        $saleReport = $saleReport->orderBy($orderBy, $desc)->limit($limit)->offset(($current_page - 1) * $limit)->get();

        return [
            'sale_report' => $saleReport,
            'pagination' => [
                'total' => (int) $total,
                'current_page' => (int) $current_page,
                'limit' => (int) $limit,
            ],
        ];
    }
}
