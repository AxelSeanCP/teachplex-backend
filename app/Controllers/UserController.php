<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class UserController extends BaseController
{
    protected $service;

    //injection happens at App\Config\Services
    public function __construct()
    {
        $this->service = service("userService");
    }

    public function index()
    {
        $users = $this->service->getAll();

        return $this->respond([
            "status" => "success",
            "data" => $users
        ], 200);
    }

    public function store()
    {
        $userData = validateRequest("users");

        $id = $this->service->add($userData);

        return $this->respond([
            "status" => "success",
            "message" => "User added successfully",
            "data" => [
                "userId" => $id
            ]
        ], 201);
    }

    public function show($id = null) 
    {
        $user = $this->service->getById($id);

        return $this->respond([
            "status" => "success",
            "data" => [
                "user" => $user,
            ]
        ], 200);
    }

    // public function update($id = null)
    // {
    //     $userData = validateRequest("users");

        
    // }

    // public function destroy($id = null)
    // {

    // }
}
