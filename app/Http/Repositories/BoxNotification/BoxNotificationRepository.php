<?php

declare(strict_types=1);

namespace App\Repositories\BoxNotification;

use App\Enums\DBConstant;
use App\Models\BoxNotification;
use App\Repositories\RepositoryAbstract;

class BoxNotificationRepository extends RepositoryAbstract implements BoxNotificationRepositoryInterface
{
    /**
     * Construct.
     *
     * @param BoxNotification $boxNotification
     */
    public function __construct(BoxNotification $boxNotification)
    {
        parent::__construct();
        $this->model = $boxNotification;
    }

    /**
     * Get box notification list of user.
     *
     * @param User user
     * @param mixed $user
     */
    public function getBoxNotificationList($user)
    {
        return $this->model->select(
            'box_notification_contents.*',
            'box_notification_contents.' . $this->getTitleLangCode($user->lang_code) . ' as title'
        )
            ->join(
                'box_notification_contents',
                'box_notification_contents.box_notification_content_id',
                'box_notifications.box_notification_content_id'
            )->where('box_notifications.user_id', $user->user_id)
            ->orderBy('box_notifications.created_at', 'desc')->get();
    }

    /**
     * Get box notification list for operation.
     *
     * @param array param
     * @param mixed $params
     * @return array data
     */
    public function getAllBoxNotificationList($params)
    {
        $limit = $params['limit'];
        $current_page = $params['current_page'];
        $orderBy = $params['orderBy'];
        $desc = $params['desc'];
        $data = $this->model::select('*');

        $total = $data->get()->count();
        $data = $data->orderBy($orderBy, $desc)->limit($limit)->offset(($current_page - 1) * $limit);

        return [
            'box_notifications' => $data->get(),
            'pagination' => [
                'total' => $total,
                'current_page' => $current_page,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Get title langcode.
     *
     * @param string langCode
     * @param mixed $langCode
     */
    private function getTitleLangCode($langCode)
    {
        switch ($langCode) {
            case DBConstant::LANG_CODE_JA:
                return 'title_ja';
            case DBConstant::LANG_CODE_EN:
                return 'title_en';
            case DBConstant::LANG_CODE_CS:
                return 'title_cs';
            case DBConstant::LANG_CODE_CT:
                return 'title_ct';
            default:
                return 'title_en';
        }
    }
}
