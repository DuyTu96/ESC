<?php

declare(strict_types=1);

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function getUserInfo();

    public function getDataEditInfoForm();

    public function getIdFavoriteShopByUser();

    public function getUserList($params, $request);

    public function show($id);

    public function destroy($id);

    public function update($id, $data);

    public function register($request);

    public function verifyToken($token);
}
