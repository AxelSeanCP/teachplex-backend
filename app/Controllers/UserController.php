<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Exception;

class UserController extends BaseController
{
    protected $userService;

    //injection happens at App\Config\Services
    public function __construct()
    {
        $this->userService = service("userService");
    }

    public function index()
    {
        try {
            $users = $this->userService->getAll();

            return $this->respond([
                "status" => "success",
                "data" => $users
            ], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store()
    {
        try {
            $userData = validateRequest("users");

            $id = $this->userService->add($userData);

            return $this->respond([
                "status" => "success",
                "message" => "User added successfully",
                "data" => [
                    "userId" => $id
                ]
            ], 201);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id = null) 
    {
        try {
            $user = $this->userService->getById($id);

            return $this->respond([
                "status" => "success",
                "data" => [
                    "user" => $user,
                ]
            ], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    // public function update($id = null)
    // {
    //     $userData = validateRequest("users");

        
    // }

    // public function destroy($id = null)
    // {

    // }
}
