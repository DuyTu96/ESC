<?php

declare(strict_types=1);

namespace App\Repositories\ShopGroupUser;

interface ShopGroupUserRepositoryInterface
{
    public function getListOfCurrentUser($request);

    public function updateOrCreate($request);

    public function delete($id);

    public function register($request);

    public function getTokenData($token);

    public function getUser($request);

    public function updateProfile($request);

    public function inviteShopGroup($request);
}
