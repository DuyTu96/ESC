<?php

declare(strict_types=1);

namespace App\Repositories\BoxNotificationContent;

use App\Models\BoxNotificationContent;
use App\Repositories\RepositoryAbstract;

class BoxNotificationContentRepository extends RepositoryAbstract implements BoxNotificationContentRepositoryInterface
{
    /**
     * Construct.
     *
     * @param BoxNotificationContent $boxNotificationContent
     */
    public function __construct(BoxNotificationContent $boxNotificationContent)
    {
        parent::__construct();
        $this->model = $boxNotificationContent;
    }

    /**
     * Get box notification list for operation.
     *
     * @param array param
     * @param mixed $params
     * @return array data
     */
    public function getAllBoxNotificationContentList($params)
    {
        $limit = $params['limit'];
        $current_page = $params['current_page'];
        $orderBy = $params['orderBy'];
        $desc = $params['desc'];
        $data = $this->model::select(
            '*',
            'title_ja as box_notification_content_title_ja'
        );

        $total = $data->get()->count();
        $data = $data->orderBy($orderBy, $desc)->limit($limit)->offset(($current_page - 1) * $limit);

        return [
            'box_notification_contents' => $data->get(),
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Get notification detail.
     * @param $id $lang
     * @param mixed $lang
     * @return array data
     */
    public function getNotificationDetail($id, $lang)
    {
        $notificationDetail = BoxNotificationContent::select(
            'box_notification_contents.title_' . $lang,
            'box_notification_contents.created_at',
            'box_notification_contents.body_' . $lang
        )
            ->where('box_notification_content_id', $id)
            ->get();

        $data = [
            'notificationDetail' => $notificationDetail,
        ];

        return $data;
    }

    public function store($data): void
    {
        $this->model->create($data);
    }

    public function update($id, $data): void
    {
        parent::update($id, $data);
    }
}
