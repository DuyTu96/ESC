<?php

declare(strict_types=1);

namespace App\Repositories\CastMember;

use App\Enums\DBConstant;
use App\Enums\PerPageLimit;
use App\Models\AnomalousCastMemberReservableTime;
use App\Models\BasicCastMemberReservableTime;
use App\Models\CastMember;
use App\Models\ImagePath;
use App\Models\Shop;
use App\Repositories\RepositoryAbstract;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class CastMemberRepository extends RepositoryAbstract implements CastMemberRepositoryInterface
{
    /**
     * Construct.
     *
     * @param CastMember $CastMember
     * @param CastMember $castMember
     */
    public function __construct(CastMember $castMember)
    {
        parent::__construct();
        $this->model = $castMember;
        $this->table = 'cast_members';
    }

    /**
     * Get cast members for index page.
     *
     * @param $request
     * @return array
     */
    public function getCastMembers($request)
    {
        $user = $request->user();
        $limit = $request->query('limit') ?? PerPageLimit::BASIC_LIMIT;
        $sortField = $request->query('sort_field') ?? 'cast_member_name';
        $sortType = ($request->query('is_descending') === 'DESC') ? 'ASC' : 'DESC';
        $currentPage = $request->query('current_page') ?? 1;

        $searchKeys = [
            'cast_member_name' => 'cast_members.name',
            'shop_name' => 'shops.shop_name_ja',
        ];

        $searchConditions = [];
        foreach ($searchKeys as $searchField => $searchColumn) {
            if ($request->query($searchField) && trim($request->query($searchField))) {
                $searchConditions[$searchColumn] = trim($request->query($searchField));
            }
        }

        $query = $this->model->select(
            'cast_members.*',
            'cast_members.name AS cast_member_name',
            'shops.shop_name_ja AS shop_name'
        );
        $query->join('shops', 'shops.shop_id', 'cast_members.shop_id');
        if ($user->authority_type == DBConstant::OC_USER_IS_ADMINISTRATOR) {
            $query->where('shops.shop_group_id', $user->shop_group_id);
        } else {
            $query->join('shop_group_user_maps', 'shop_group_user_maps.shop_id', 'cast_members.shop_id')
                ->where('shop_group_user_maps.sg_user_id', $user->sg_user_id);
        }

        $query->with(['image_paths' => function ($q) {
            $q->where("image_type", DBConstant::IMAGE_TYPE_CAST_MEMBER_PROFILE);
        }]);

        $query->groupBy('cast_members.cast_member_id')
            ->orderBy($sortField, $sortType);

        foreach ($searchConditions as $field => $value) {
            $query->where($field, 'like', '%' . $value . '%');
        }

        $total = count($query->get());
        $castMembers = $query->limit($limit)->offset(($currentPage - 1) * $limit)->get();

        foreach ($castMembers as $key => $castMember) {
            $imagePaths = $castMember->image_paths;
            if (count($imagePaths) > 0) {
                foreach ($imagePaths as $imagePath) {
                    if ($imagePath->dir_path == sprintf(ImagePath::IMAGE_PATH_SHOP_MENU_CAST_MEMBER, $castMember->shop_id, $castMember->cast_member_id)) {
                        $imageService = new ImageService();
                        $castMember['image_url'] = $imageService->getS3Url($imagePath->dir_path . $imagePath->file_name);
                        $castMembers[$key] = $castMember;
                    }
                }
            } else {
                $castMember['image_url'] = '';
                $castMembers[$key] = $castMember;
            }
        }

        return [
            'cast_members' => $castMembers,
            'pagination' => [
                'total' => $total,
                'current_page' => $currentPage,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Get cast member detail.
     *
     * @param $id
     * @return JsonResponse
     **/
    public function getDetail($id)
    {
        $castMember = $this->model->select(
                'cast_members.*',
                'cast_members.name AS cast_member_name',
                'shops.shop_name_ja AS shop_name'
            )->with([
                'basic_cast_member_reservable_time',
                'anomalous_cast_member_reservable_times',
                'image_paths' => function ($q) {
                    $q->where("image_type", DBConstant::IMAGE_TYPE_CAST_MEMBER_PROFILE);
                }
            ])->join('shops', 'shops.shop_id', 'cast_members.shop_id')
            ->where('cast_members.cast_member_id', $id)
            ->where('cast_members.is_archived', DBConstant::ARCHIVE_FLAG_NOT_ARCHIVED)->first();

        $imagePaths = $castMember->image_paths;
        foreach ($imagePaths as $imagePath) {
            if ($imagePath->dir_path == sprintf(ImagePath::IMAGE_PATH_SHOP_MENU_CAST_MEMBER, $castMember->shop_id, $castMember->cast_member_id)) {
                $imageService = new ImageService();
                $castMember['image_url'] = $imageService->getS3Url($imagePath->dir_path . $imagePath->file_name);
                break;
            }
        }

        return $castMember;
    }

    /**
     * Edit cast member.
     *
     * @param $request
     * @return array
     **/
    public function edit($request)
    {
        $castMember = json_decode($request->input('cast_member'), true);
        $castMemberId = $castMember['cast_member_id'];
        $shopId = $castMember['shop_id'];
        if (!empty($request->file('file'))) {
            ImagePath::where('shop_id', $shopId)
                ->where('image_type', DBConstant::IMAGE_TYPE_CAST_MEMBER_PROFILE)
                ->where('dir_path', sprintf(ImagePath::IMAGE_PATH_SHOP_MENU_CAST_MEMBER, $shopId, $castMemberId))->delete();
            $imageFile = $request->file('file');
            $imageService = new ImageService();
            $imageService->uploadImageToS3(
                $shopId,
                $imageFile,
                DBConstant::IMAGE_TYPE_CAST_MEMBER_PROFILE,
                ImagePath::where('display_order', '<', DBConstant::IMAGE_MAX_DISPLAY_ORDER)
                    ->where('shop_id', $shopId)->max('display_order') + 1,
                $castMemberId
            );
        } else if (empty($castMember['image_url'])) {
            ImagePath::where('shop_id', $shopId)
                ->where('image_type', DBConstant::IMAGE_TYPE_CAST_MEMBER_PROFILE)
                ->where('dir_path', sprintf(ImagePath::IMAGE_PATH_SHOP_MENU_CAST_MEMBER, $shopId, $castMemberId))->delete();
        }
        $oldCastMember = $this->model->with(['anomalous_cast_member_reservable_times'])
            ->where('cast_member_id', $castMemberId)
            ->where('is_archived', DBConstant::ARCHIVE_FLAG_NOT_ARCHIVED)->first();
        $oldAnomalousReservableTimes = $oldCastMember['anomalous_cast_member_reservable_times'];
        unset($castMember['cast_member_id']);
        $basicReservableTime = $castMember['basic_cast_member_reservable_time'];
        $basicReservableTimeId = $basicReservableTime['id'];
        unset($basicReservableTime['id']);
        $anomalousReservableTimes = $castMember['anomalous_cast_member_reservable_times'];
        DB::beginTransaction();

        try {
            BasicCastMemberReservableTime::find($basicReservableTimeId)
                ->update($basicReservableTime);
            foreach ($oldAnomalousReservableTimes as $oldAnomalousReservableTime) {
                $exist = false;
                foreach ($anomalousReservableTimes as $anomalousReservableTime) {
                    if (
                        isset($anomalousReservableTime['id']) &&
                        $oldAnomalousReservableTime['id'] == $anomalousReservableTime['id']
                    ) {
                        $exist = true;

                        break;
                    }
                }
                if (!$exist) {
                    AnomalousCastMemberReservableTime::find($oldAnomalousReservableTime['id'])->delete();
                }
            }
            foreach ($anomalousReservableTimes as $anomalousReservableTime) {
                if (isset($anomalousReservableTime['id'])) {
                    $anomalousReservableTimeId = $anomalousReservableTime['id'];
                    unset($anomalousReservableTime['id']);
                    AnomalousCastMemberReservableTime::find($anomalousReservableTimeId)
                        ->update($anomalousReservableTime);
                } else {
                    $anomalousReservableTime['cast_member_id'] = $castMemberId;
                    AnomalousCastMemberReservableTime::create($anomalousReservableTime);
                }
            }
            $this->model->find($castMemberId)->update($castMember);
            DB::commit();

            return ['status' => true, 'message' => null];
        } catch (\Exception $e) {
            DB::rollback();

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create cast member.
     *
     * @param $request
     * @return array
     **/
    public function create($request)
    {
        $castMember = json_decode($request->input('cast_member'), true);
        $shopId = $castMember['shop_id'];
        $basicReservableTime = $castMember['basic_cast_member_reservable_time'];
        $anomalousReservableTimes = $castMember['anomalous_cast_member_reservable_times'];
        DB::beginTransaction();

        try {
            $castMember['display_order'] = CastMember::where('shop_id', $shopId)->max('display_order') + 1;
            $castMember = $this->model->create($castMember);
            $castMemberId = $castMember['cast_member_id'];
            $basicReservableTime['cast_member_id'] = $castMemberId;
            BasicCastMemberReservableTime::create($basicReservableTime);
            foreach ($anomalousReservableTimes as $anomalousReservableTime) {
                $anomalousReservableTime['cast_member_id'] = $castMemberId;
                AnomalousCastMemberReservableTime::create($anomalousReservableTime);
            }
            if (!empty($request->file('file'))) {
                $imageFile = $request->file('file');
                $imageService = new ImageService();
                $imageService->uploadImageToS3(
                    $shopId,
                    $imageFile,
                    DBConstant::IMAGE_TYPE_CAST_MEMBER_PROFILE,
                    ImagePath::where('display_order', '<', DBConstant::IMAGE_MAX_DISPLAY_ORDER)
                        ->where('shop_id', $shopId)->max('display_order') + 1,
                    $castMemberId
                );
            }
            DB::commit();

            return ['status' => true, 'message' => null];
        } catch (\Exception $e) {
            DB::rollback();

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete cast member.
     *
     * @param Request $request
     * @return JsonResponse
     **/
    public function delete($request)
    {
        $castMemberId = $request->input('cast_member_id');
       
        $castMember = $this->model->with(['anomalous_cast_member_reservable_times', 'basic_cast_member_reservable_time'])
            ->where('cast_member_id', $castMemberId)
            ->where('is_archived', DBConstant::ARCHIVE_FLAG_NOT_ARCHIVED)->first();
        $basicReservableTime = $castMember['basic_cast_member_reservable_time'];
        $anomalousReservableTimes = $castMember['anomalous_cast_member_reservable_times'];
        DB::beginTransaction();

        try {
            ImagePath::where('shop_id', $castMember['shop_id'])
                ->where('image_type', DBConstant::IMAGE_TYPE_CAST_MEMBER_PROFILE)
                ->where('dir_path', sprintf(ImagePath::IMAGE_PATH_SHOP_MENU_CAST_MEMBER, $castMember['shop_id'], $castMemberId))->delete();
            BasicCastMemberReservableTime::find($basicReservableTime['id'])->delete($basicReservableTime);
            foreach ($anomalousReservableTimes as $anomalousReservableTime) {
                AnomalousCastMemberReservableTime::find($anomalousReservableTime['id'])->delete();
            }
            $this->model->find($castMemberId)->update(['is_archived' => DBConstant::ARCHIVE_FLAG_ARCHIVED]);
            DB::commit();

            return ['status' => true, 'message' => null];
        } catch (\Exception $e) {
            DB::rollback();

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get all shops for shop group user.
     *
     * @param Request $request
     * @return JsonResponse
     **/
    public function getAllShopByShopGroupUser($request)
    {
        $user = $request->user();
        if ($user->authority_type == DBConstant::OC_USER_IS_ADMINISTRATOR) {
            return Shop::where('shop_group_id', $user->shop_group_id)->get();
        }

        return Shop::select('shops.*')
            ->join('shop_group_user_maps', 'shop_group_user_maps.shop_id', 'shops.shop_id')
            ->where('shop_group_user_maps.sg_user_id', $user->sg_user_id)->get();
    }
}
