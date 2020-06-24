<?php

declare(strict_types=1);

namespace App\Repositories\OperatingCompanyUser;

use App\Repositories\RepositoryInterface;

interface OperatingCompanyUserRepositoryInterface extends RepositoryInterface
{
    public function getOCUserList($params, $request);

    public function updateOrCreate($data);

    public function invite($request);

    public function register($request);

    public function delete($id);

    public function getProfile();

    public function updateProfile($data);

    public function checkUserInvite($token);
}
