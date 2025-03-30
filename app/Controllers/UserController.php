<?php

namespace App\Controllers;

use App\Controllers\ApiBaseController;
use App\Exceptions\NotFoundError;
use App\Models\User;
use Exception;

class UserController extends ApiBaseController
{
    public function index()
    {
        try {
            $userModel = new User();
            $users = $userModel->findAll();

            if (count($users) == 0) {
                throw new NotFoundError("Users not found");
            }

            return $this->respond([
                "status" => "success",
                "data" => $users
            ], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}
