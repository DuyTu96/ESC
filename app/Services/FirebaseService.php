<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Like;
use App\Models\User;
use Exception;
use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseService
{
    protected $firebase;

    public function __construct()
    {
        $jsonFile = 'firebase/service_accounts.json'; // đường dẫn file json chứa thông tin service accounts mà chúng ta vừa tải về
        $serviceAccount = ServiceAccount::fromJsonFile(base_path($jsonFile)); // Load file service account json
        $this->firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://airport-match.firebaseio.com') // Database Uri bạn lấy ở trong Web Setup (trong tab Authentication) của firebase nhé
            ->create(); // Khởi tạo firebase
    }

    public function createCustomToken($uid, array $additionalClaims = [])
    {
        try {
            $uid = (string) $uid;
            $auth = $this->firebase->getAuth(); // Khởi tạo firebase authenticate
            $customToken = $auth->createCustomToken($uid, $additionalClaims); // Tạo custom token dựa vào uid, additionalClaims là các thông tin bạn cần để check thêm ví dụ tài khoản premiumAccount chẳng hạn
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return (string) $customToken;
    }

    public function createUserNode(User $user): void
    {
        if (!empty($user)) {
            $user_id = $this->getUserIdFormat($user->user_id);
            $data = [
                'likes' => [
                ], // When like room created will add like id to user
                'timestamp' => Database::SERVER_TIMESTAMP,
            ];

            $database = $this->firebase->getDatabase();
            $like_created = $database->getReference('users/' . $user_id);
            $like_created->update($data);
        }
    }

    public function createLikeRoom(Like $like): void
    {
        if (!empty($like)) {
            $from_user_id = $this->getUserIdFormat($like->from_user_id);
            $to_user_id = $this->getUserIdFormat($like->to_user_id);
            $like_id = $this->getLikeIdFormat($like->like_id);
            $data = [
                'read_user_ids' => [
                    $from_user_id => true,
                ], // When create like set user create to read
                'read_new_user_ids' => [
                    $from_user_id => true,
                ], // When create like set user create to read
                'is_matched' => false,
                'is_blocked' => false, // For block conversation
                'user_ids' => [
                    $from_user_id => true,
                    $to_user_id => true,
                ],
                'timestamp' => Database::SERVER_TIMESTAMP,
            ];

            $database = $this->firebase->getDatabase();
            $like_created = $database->getReference('likes/' . $like_id);
            $like_created->set($data);
        }
    }

    public function updateMatchedRoom(Like $like): void
    {
        if (!empty($like)) {
            $like_id = $this->getLikeIdFormat($like->like_id);
            $data = [
                'is_matched' => $like->matched_status == Like::IS_THANK ? true : false,
                'timestamp' => Database::SERVER_TIMESTAMP,
            ];

            $database = $this->firebase->getDatabase();
            $like_created = $database->getReference('likes/' . $like_id);
            $like_created->update($data);
        }
    }

    public function test(): void
    {
        $database = $this->firebase->getDatabase();
        $users = $database->getReference('likes')->getValue();
        dd($users);
        // $users->push([
        //     'name' => 'My Application',
        //     'emails' => [
        //         'support' => 'support@domain.tld',
        //         'sales' => 'sales@domain.tld',
        //     ],
        //     'website' => 'https://app.domain.tld',
        // ]);
        // $auth = $this->firebase->getAuth();
        // $users = $auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);
        foreach ($users as $user) {
            // @var \Kreait\Firebase\Auth\UserRecord $user
            dd($user->getValue());
        }

        // dd($users);
    }

    /**
     * Get userid format for store Firebase.
     *
     * @param string $user_id
     *
     * @return  string
     */
    private function getUserIdFormat($user_id = '')
    {
        return 'user_' . $user_id;
    }

    /**
     * Get likeid format for store Firebase.
     *
     * @param string $like_id
     *
     * @return  string
     */
    private function getLikeIdFormat($like_id = '')
    {
        return 'like_' . $like_id;
    }
}
