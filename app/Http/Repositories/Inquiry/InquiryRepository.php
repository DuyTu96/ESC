<?php

declare(strict_types=1);

namespace App\Repositories\Inquiry;

use App\Enums\Constant;
use App\Enums\DBConstant;
use App\Enums\ErrorType;
use App\Enums\PerPageLimit;
use App\Models\Inquiry;
use App\Models\InquiryMessage;
use App\Repositories\RepositoryAbstract;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;

class InquiryRepository extends RepositoryAbstract implements InquiryRepositoryInterface
{
    /**
     * Get list OP Inquiry.
     * @author skrum
     * @param $request
     * @return array
     */
    public function getInquiries($request)
    {
        $limit = $request->query('limit') ?? PerPageLimit::BASIC_LIMIT;
        $sortField = $request->query('sort_field') ?? 'last_send_datetime';
        $sortType = ($request->query('is_descending') === 'DESC') ? 'DESC' : 'ASC';
        $currentPage = $request->query('current_page') ?? 1;
        $filters = [
            'inquiries.title' => [
                'where' => 'like',
                'value' => $request->query('title') ?? '',
            ],
            'inquiries.from_user_id' => [
                'where' => '=',
                'value' => $request->query('from_user_id') ?? '',
            ],
            'users.name' => [
                'where' => 'like',
                'value' => $request->query('from_user_name') ?? '',
            ],
            'inquiries.from_shop_id' => [
                'where' => '=',
                'value' => $request->query('from_shop_id') ?? '',
            ],
            'from_shop.shop_name_ja' => [
                'where' => 'like',
                'value' => $request->query('from_shop_name') ?? '',
            ],
            'inquiries.from_shop_group_id' => [
                'where' => '=',
                'value' => $request->query('from_shop_group_id') ?? '',
            ],
            'sg.name' => [
                'where' => 'like',
                'value' => $request->query('from_shop_group_name') ?? '',
            ],
            'inquiries.to_shop_id' => [
                'where' => '=',
                'value' => $request->query('to_shop_id') ?? '',
            ],
            'to_shop.shop_name_ja' => [
                'where' => 'like',
                'value' => $request->query('to_shop_name') ?? '',
            ],
        ];

        $query = Inquiry::select('inquiries.*',
            'users.name AS from_user_name',
            'from_shop.shop_name_ja AS from_shop_name',
            'sg.name AS from_shop_group_name',
            'to_shop.shop_name_ja AS to_shop_name')
            ->where('inquiries.from_system_flag', DBConstant::INQUIRY_NOT_FROM_SYSTEM)
            ->leftJoin('users', 'users.user_id', '=', 'inquiries.from_user_id')
            ->leftJoin('shops AS from_shop', 'from_shop.shop_id', '=', 'inquiries.from_shop_id')
            ->leftJoin('shop_groups as sg', 'sg.shop_group_id', '=', 'inquiries.from_shop_group_id')
            ->leftJoin('shops as to_shop', 'to_shop.shop_id', '=', 'inquiries.to_shop_id')
            ->orderBy($sortField, $sortType);

        foreach ($filters as $key => $condition) {
            if (!$condition['value']) {
                continue;
            }
            if ($condition['where'] == 'like') {
                $query->where($key, $condition['where'], '%' . $condition['value'] . '%');
            } else {
                $query->where($key, $condition['where'], $condition['value']);
            }
        }

        $total = $query->count();
        $inquires = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();
        return [
            'inquiries' => $inquires,
            'pagination' => [
                'total' => $total,
                'current_page' => $currentPage,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Get detail of OP inquiry.
     * @author skrum
     * @param $id
     * @return array
     * */
    public function getDetail($id)
    {
        $inquiry = Inquiry::find($id);
        if ($inquiry == null) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4041,
                    'msg' => __('errors.MSG_4041'),
                    'res_status' => ErrorType::STATUS_4041,
                ],
            ];
        }
        if ($inquiry->from_system_flag === 1) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4010,
                    'msg' => __('errors.MSG_4010'),
                    'res_status' => ErrorType::STATUS_4010,
                ],
            ];
        }
        if ($inquiry->from_operating_company_flag == DBConstant::INQUIRY_FROM_OPERATING_COMPANY) {
            $inquiryUpdate = [
                'read_flag_for_from' => DBConstant::READ_FLAG_READ,
            ];
        } else {
            $inquiryUpdate = [
                'read_flag_for_to' => DBConstant::READ_FLAG_READ,
            ];
        }
        $inquiry->update($inquiryUpdate);
        $this->updateOPReadMessageStatus($id);
        $inquiryMessages = InquiryMessage::select(
            'inquiry_messages.*',
            'inquiry_messages.from_oc_user_id AS from_oc_user',
            'inquiry_messages.from_system_flag AS from_s_flag',
            'u.name AS from_user_name',
            'sgu.name AS from_sg_user_name',
            'ocu.name AS from_oc_user_name'
        )->leftJoin('users AS u', 'u.user_id', '=', 'inquiry_messages.from_user_id')
            ->leftJoin('shop_group_users AS sgu', 'sgu.sg_user_id', '=', 'inquiry_messages.from_sg_user_id')
            ->leftJoin('operating_company_users AS ocu', 'ocu.oc_user_id', '=', 'inquiry_messages.from_oc_user_id')
            ->where('inquiry_messages.inquiry_id', $id)->orderBy('inquiry_messages.timestamp')->get();

        return [
            'success' => true,
            'inquiry' => $inquiry,
            'inquiry_messages' => $inquiryMessages
        ];
    }

    /**
     * Update Read status in InquiryMessage table
     * @param $inquiryId
     */
    public function updateOPReadMessageStatus($inquiryId)
    {
        // TODO: Currently timestamp is auto updating when update data, need check it and remove in array data update
        $inquiry = Inquiry::find($inquiryId);
        if ($inquiry->from_operating_company_flag == DBConstant::INQUIRY_FROM_OPERATING_COMPANY) {
            InquiryMessage::where('inquiry_id', $inquiryId)
                ->where('read_flag_for_from', DBConstant::READ_FLAG_UNREAD)
                ->update([
                    'read_flag_for_from' => DBConstant::READ_FLAG_READ,
                    'read_flag_for_to' => DBConstant::READ_FLAG_READ,
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]);
        } else {
            InquiryMessage::where('inquiry_id', $inquiryId)
                ->where('read_flag_for_to', DBConstant::READ_FLAG_UNREAD)
                ->update([
                    'read_flag_for_from' => DBConstant::READ_FLAG_READ,
                    'read_flag_for_to' => DBConstant::READ_FLAG_READ,
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]);
        }
    }

    /**
     * Get detail of inquiry.
     * @author skrum
     * @param $request
     * @return array
     * */
    public function create($request)
    {
        $shopId = $request->input('to_shop_id');
        $title = $request->input('title');
        $message = $request->input('message');

        $inquiryData = [
            'to_shop_id' => $shopId,
            'title' => $title,
            'from_operating_company_flag' => DBConstant::INQUIRY_FROM_OPERATING_COMPANY,
            'read_flag_for_from' => DBConstant::READ_FLAG_READ,
            'last_send_datetime' => Carbon::now()->toDateTimeString()
        ];
        DB::beginTransaction();

        try {
            $inquiry = Inquiry::create($inquiryData);

            $inquiryMessage = [
                'inquiry_id' => $inquiry->id,
                'message' => $message,
                'from_oc_user_id' => $request->user()->oc_user_id,
                'timestamp' => $inquiry->last_send_datetime,
                'read_flag_for_from' => DBConstant::READ_FLAG_READ,
            ];
            InquiryMessage::create($inquiryMessage);
            DB::commit();

            return ['status' => true, 'message' => null];
        } catch (\Exception $e) {
            DB::rollback();

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * OP Send a message.
     * @author skrum
     * @param $request
     * @return array
     * */
    public function send($request)
    {
        $inquiryId = $request->input('id');
        $inquiry = Inquiry::find($inquiryId);
        $inquiryMessage = [
            'from_oc_user_id' => $request->user()->oc_user_id,
            'inquiry_id' => $inquiryId,
            'timestamp' => Carbon::now()->toDateTimeString(),
            'message' => $request->input('message'),
        ];
        if ($inquiry->from_operating_company_flag === DBConstant::INQUIRY_FROM_OPERATING_COMPANY) {
            $inquiryMessage['read_flag_for_from'] = DBConstant::READ_FLAG_READ;
            $inquiryMessage['read_flag_for_to'] = DBConstant::READ_FLAG_UNREAD;
        } else {
            $inquiryMessage['read_flag_for_from'] = DBConstant::READ_FLAG_UNREAD;
            $inquiryMessage['read_flag_for_to'] = DBConstant::READ_FLAG_READ;
        }

        try {
            $inquiryMsg =  InquiryMessage::create($inquiryMessage);
            if ($inquiry->from_operating_company_flag === DBConstant::INQUIRY_FROM_OPERATING_COMPANY) {
                $inquiRyUpdate = [
                    'read_flag_for_from' => DBConstant::READ_FLAG_READ,
                    'read_flag_for_to' => DBConstant::READ_FLAG_UNREAD,
                    'last_send_datetime' => $inquiryMsg->timestamp,
                ];
            } else {
                $inquiRyUpdate = [
                    'read_flag_for_from' => DBConstant::READ_FLAG_UNREAD,
                    'read_flag_for_to' => DBConstant::READ_FLAG_READ,
                    'last_send_datetime' => $inquiryMsg->timestamp,
                ];
            }
            $inquiry->update($inquiRyUpdate);

            return ['status' => true, 'message' => null];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get number inquiry which don't read of OP.
     * @return int number
     **/
    public function getNumberInquiryNoRead()
    {
        $totalMsgUnReadSend = Inquiry::where('from_operating_company_flag', DBConstant::INQUIRY_FROM_OPERATING_COMPANY)
            ->where('read_flag_for_from', DBConstant::READ_FLAG_UNREAD)
            ->count();
        $totalMsgUnReadReceive = Inquiry::where('to_operating_company_flag', DBConstant::INQUIRY_TO_OPERATING_COMPANY)
            ->where('read_flag_for_to', DBConstant::READ_FLAG_UNREAD)
            ->count();

        return $totalMsgUnReadSend + $totalMsgUnReadReceive;
    }

    /**
     * Get shop inquiries list.
     * @author huydn
     * @param $request
     * @return array
     */
    public function getShopInquirieslist($request)
    {
        $limit = $request->query('limit') ?? Constant::BASIC_LIMIT;
        $currentPage = $request->query('current_page') ?? 1;
        $sortField = $request->query('sort_field') ?? 'last_send_datetime';
        $sortType = ($request->query('is_descending') === 'DESC') ? 'DESC' : 'ASC';
        $user = $request->user();
        $userType = $user->authority_type;
        $shopGroupId = $user->shop_group_id;
        if ($userType == DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR) {
            $query = Inquiry::select(
                'inquiries.id',
                'inquiries.to_operating_company_flag',
                'inquiries.from_shop_group_id',
                'inquiries.read_flag_for_from',
                'inquiries.title',
                'inquiries.last_send_datetime'
            )->where('from_shop_group_id', $shopGroupId)->orderBy($sortField, $sortType);
        } else {
            $shopId = $user->usersMapSG->first()->shop_id ?? false;
            if (!$shopId) return false;
            $query = Inquiry::select(
                'inquiries.id',
                'inquiries.from_system_flag',
                'inquiries.to_operating_company_flag',
                'inquiries.from_operating_company_flag',
                'inquiries.to_shop_id',
                'inquiries.title',
                'inquiries.read_flag_for_from',
                'inquiries.read_flag_for_to',
                'inquiries.last_send_datetime'
            )
            ->where('from_shop_id', $shopId)
            ->orWhere('to_shop_id', $shopId)
            ->orderBy($sortField, $sortType);
        }

        $title = $request->query('title');
        if ($title) {
            $query = $query->where('title', 'like', '%' . $title . '%');
        }

        $total = $query->get()->count();
        $inquiries = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();

        return [
            'inquiries' => $inquiries,
            'pagination' => [
                'total' => $total,
                'current_page' => $currentPage,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Get detail of shop inquiry.
     * @author huydn
     * @param $id
     * @return array
     * */
    public function getShopInquiryDetail($id)
    {
        $inquiry = Inquiry::find($id);
        
        if ($inquiry == null) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4041,
                    'msg' => __('errors.MSG_4041'),
                    'res_status' => ErrorType::STATUS_4041,
                ],
            ];
        }
        $isPass = $this->checkShopAuthority($inquiry);
        if (!$isPass) {
            return [
                'success' => false,
                'data' => [
                    'err_code' => ErrorType::CODE_4010,
                    'msg' => __('errors.MSG_4010'),
                    'res_status' => ErrorType::STATUS_4010,
                ],
            ];
        }
        if ($inquiry->to_operating_company_flag == DBConstant::INQUIRY_TO_OPERATING_COMPANY) {
            $inquiryUpdate = [
                'read_flag_for_from' => DBConstant::READ_FLAG_READ,
            ];
        } else {
            $inquiryUpdate = [
                'read_flag_for_to' => DBConstant::READ_FLAG_READ,
            ];
        }
        $inquiry->update($inquiryUpdate);
        $this->updateShopReadMessageStatus($id);
        $inquiryMessages = InquiryMessage::select(
            'inquiry_messages.*',
            'inquiry_messages.from_oc_user_id AS from_oc_user',
            'inquiry_messages.from_system_flag AS from_s_flag',
            'sgu.name AS from_sg_user_name',
        )
            ->leftJoin('shop_group_users AS sgu', 'sgu.sg_user_id', '=', 'inquiry_messages.from_sg_user_id')
            ->where('inquiry_messages.inquiry_id', $id)->orderBy('inquiry_messages.timestamp')->get();

        return [
            'success' => true,
            'inquiry' => $inquiry,
            'inquiry_messages' => $inquiryMessages
        ];
    }

    /**
     * Check shop authority_type
     * @param inquiry
     */
    public function checkShopAuthority($inquiry)
    {
        $authority = Auth::user()->authority_type;
        if ($authority === DBConstant::SHOP_GROUP_USER_SHOP_MANAGER) {
            $shopId = Auth::user()->usersMapSG->first()->shop_id;
            if ((empty($inquiry->from_shop_id) && $inquiry->to_shop_id !== $shopId)
                || (empty($inquiry->to_shop_id) && $inquiry->from_shop_id !== $shopId)) {
                return false;
            }

            return true;
        } else {
            $shopGroupId = Auth::user()->shop_group_id;
            if ($inquiry->from_shop_group_id !== $shopGroupId) {
                return false;
            }

            return true;
        }
    }

    /**
     * Update Read status in InquiryMessage table
     * @param $inquiryId
     */
    public function updateShopReadMessageStatus($inquiryId)
    {
        // TODO: Currently timestamp is auto updating when update data, need check it and remove in array data update
        $inquiry = Inquiry::find($inquiryId);
        if ($inquiry->to_operating_company_flag == DBConstant::INQUIRY_TO_OPERATING_COMPANY) {
            InquiryMessage::where('inquiry_id', $inquiryId)
                ->where('read_flag_for_from', DBConstant::READ_FLAG_UNREAD)
                ->update([
                    'read_flag_for_from' => DBConstant::READ_FLAG_READ,
                    'read_flag_for_to' => DBConstant::READ_FLAG_READ,
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]);
        } else {
            InquiryMessage::where('inquiry_id', $inquiryId)
                ->where('read_flag_for_to', DBConstant::READ_FLAG_UNREAD)
                ->update([
                    'read_flag_for_from' => DBConstant::READ_FLAG_READ,
                    'read_flag_for_to' => DBConstant::READ_FLAG_READ,
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]);
        }
    }

    /**
     * Create shop inquiry.
     * @author huydn
     * @param $request
     * @return array
     * */
    public function createShopInquiry($request)
    {
        $user = $request->user();
        $userType = $user->authority_type;
        $userId = $user->sg_user_id;
        $shopGroupId = $user->shop_group_id;
        $title = $request->input('title');
        $message = $request->input('message');
        if ($userType == DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR) {
            $inquiry = [
                'from_shop_group_id' => $shopGroupId,
                'title' => $title,
                'to_operating_company_flag' => DBConstant::INQUIRY_TO_OPERATING_COMPANY,
                'read_flag_for_from' => DBConstant::READ_FLAG_READ,
                'last_send_datetime' => Carbon::now()->toDateTimeString(),
            ];
        } else {
            $shopId = $user->usersMapSG->first()->shop_id;
            $inquiry = [
                'from_shop_id' => $shopId,
                'title' => $title,
                'to_operating_company_flag' => DBConstant::INQUIRY_TO_OPERATING_COMPANY,
                'read_flag_for_from' => DBConstant::READ_FLAG_READ,
                'last_send_datetime' => Carbon::now()->toDateTimeString(),
            ];
        }
        DB::beginTransaction();

        try {
            $inquiry = Inquiry::create($inquiry);
            if ($inquiry == null) {
                DB::rollback();

                return [
                    'success' => false,
                    'data' => [
                        'err_code' => ErrorType::CODE_5004,
                        'msg' => __('errors.MSG_5004'),
                        'res_status' => ErrorType::STATUS_5004,
                    ],
                ];
            }
            $inquiryMessage = [
                'inquiry_id' => $inquiry->id,
                'message' => $message,
                'from_sg_user_id' => $userId,
                'timestamp' => $inquiry->last_send_datetime,
                'read_flag_for_from' => DBConstant::READ_FLAG_READ,
            ];
            InquiryMessage::create($inquiryMessage);

            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollback();

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
     * Shop Send a message.
     * @author huydn
     * @param $request
     * @return array
     * */
    public function sendMessageResponse($request)
    {
        $userId = $request->user()->sg_user_id;
        $message = $request->input('message');
        $inquiryId = $request->input('id');
        $inquiry = Inquiry::find($inquiryId);
        $inquiryMessage = [
            'from_sg_user_id' => $userId,
            'inquiry_id' => $inquiryId,
            'timestamp' => Carbon::now()->toDateTimeString(),
            'message' => $message,
        ];
        if ($inquiry->to_operating_company_flag === DBConstant::INQUIRY_TO_OPERATING_COMPANY) {
            $inquiryMessage['read_flag_for_from'] = DBConstant::READ_FLAG_READ;
            $inquiryMessage['read_flag_for_to'] = DBConstant::READ_FLAG_UNREAD;
        } else {
            $inquiryMessage['read_flag_for_from'] = DBConstant::READ_FLAG_UNREAD;
            $inquiryMessage['read_flag_for_to'] = DBConstant::READ_FLAG_READ;
        }

        try {
            $inquiryMsg = InquiryMessage::create($inquiryMessage);
            if ($inquiry->to_operating_company_flag === DBConstant::INQUIRY_TO_OPERATING_COMPANY) {
                $inquiryUpdate = [
                    'read_flag_for_from' => DBConstant::READ_FLAG_READ,
                    'read_flag_for_to' => DBConstant::READ_FLAG_UNREAD,
                    'last_send_datetime' => $inquiryMsg->timestamp
                ];
            } else {
                $inquiryUpdate = [
                    'read_flag_for_from' => DBConstant::READ_FLAG_UNREAD,
                    'read_flag_for_to' => DBConstant::READ_FLAG_READ,
                    'last_send_datetime' => $inquiryMsg->timestamp
                ];
            }
            $inquiry->update($inquiryUpdate);
            
            return ['success' => true];
        } catch (\Exception $e) {
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
     * Get number inquiry which don't read of SHOP.
     * @author huydn
     * @param $request
     * @return int number
     **/
    public function getNumberShopInquiryNoRead($request)
    {
        $user = $request->user();
        $userType = $user->authority_type;
        $shopGroupId = $user->shop_group_id;
        if ($userType == DBConstant::SHOP_GROUP_USER_SHOP_ADMINISTRATOR) {
            return Inquiry::where('from_shop_group_id', $shopGroupId)->where('read_flag_for_from', DBConstant::READ_FLAG_UNREAD)->count();
        } else {
            $shopId = $user->usersMapSG->first()->shop_id ?? false;
            $totalMsgUnReadReceive = Inquiry::where('to_shop_id', $shopId)->where('read_flag_for_to', DBConstant::READ_FLAG_UNREAD)->count();
            $totalMsgUnReadSend = Inquiry::where('from_shop_id', $shopId)
                ->where('to_operating_company_flag', DBConstant::INQUIRY_TO_OPERATING_COMPANY)
                ->where('read_flag_for_from', DBConstant::READ_FLAG_UNREAD)
                ->count();
            
            return $totalMsgUnReadReceive + $totalMsgUnReadSend;
        }
    }

    public function updateReadShopInquiry($id)
    {
        return Inquiry::where('id', $id)->where('from_operating_company_flag', DBConstant::INQUIRY_FROM_OPERATING_COMPANY)->update(['is_read' => DBConstant::READ_FLAG_READ]);
    }
}
