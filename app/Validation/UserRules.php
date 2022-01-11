<?php

namespace App\Validation;

use App\Models\UserModel;

class UserRules{

    public function customer($userId)
    {
        return $this->checkType($userId, "customer");
    }
    public function provider($userId)
    {
        return $this->checkType($userId, "provider");
    }

    public function checkType($userId, $type)
    {
        $userModel = new UserModel();
        $user = $userModel->asObject()->where("type", $type)->find($userId);

        return $user !== null;
    }

}