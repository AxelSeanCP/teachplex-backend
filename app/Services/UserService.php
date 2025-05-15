<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Models\User;
use App\Exceptions\NotFoundError;
use App\Exceptions\UnauthorizedError;

class UserService extends BaseService
{
    protected $model;

    public function __construct(User $userModel)
    {
        $this->model = $userModel;
    }

    public function verifyUserCredentials($email, $password) 
    {
        $user = $this->model->where("email", $email)->first();
        
        if (!$user) {
            throw new UnauthorizedError("Login failed. Email not found");
        }

        $hashedPassword = $user->password;
        $match = password_verify($password, $hashedPassword);

        if (!$match) {
            throw new UnauthorizedError("Login failed. Password is wrong");
        }

        return $user->id;
    }

    public function verifyNewUsername($name)
    {
        $user = $this->model->where("name", $name)->first();

        if ($user) {
            throw new BadRequestError("User with this name already exists");
        }

        return $user;
    }

    public function add($userData)
    {
        $this->verifyNewUsername($userData["name"]);

        $id = $this->generateId("user");
        $hashedPassword = password_hash($userData["password"], PASSWORD_BCRYPT);
        
        $data = [
            "id" => $id,
            "name" => $userData["name"],
            "password" => $hashedPassword,
            "email" => $userData["email"],
        ];
        
        $this->model->insert($data);
        
        return $id;
    }

    public function getAll()
    {
        $users = $this->model
        ->select("id, name, email, role, created_at, updated_at")
        ->findAll();

        if (empty($users)) {
            throw new NotFoundError("Users not found");
        }

        return $users;
    }

    public function getById($id)
    {
        $user = $this->model
        ->select("id, name, email, role, created_at, updated_at")
        ->find($id);

        if (!$user) {
            throw new NotFoundError("User not found");
        }

        return $user;
    }

    // public function edit($id)
    // {
    //     $user = $this->getById($id);

    //     if ($user["id"])
    // }

    // public function delete($id)
    // {

    // }
}