<?php

namespace App\Controllers;

use App\Controllers\ApiBaseController;
use App\Exceptions\NotFoundError;
use CodeIgniter\Api\ResponseTrait;
use Exception;

class TestApiController extends ApiBaseController
{
    use ResponseTrait;

    public function index()
    {
        try {
            $users = [["nama" => "Axel", "umur" => 21], ["nama" => "Meltryllis", "umur" => 22]];
            // throw new NotFoundError("Users not found");
            // return $this->response($users, "Users retrieved successfully");
            return $this->respond([
                "status" => "success",
                "data" => $users
            ], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function testerror()
    {
        return $this->handleException(new Exception("You are stupid"));
    }
}
