<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Models\User;
use App\Exceptions\NotFoundError;
use App\Exceptions\UnauthorizedError;

class UserService extends BaseService
{
    protected $userModel;

    public function __construct(User $userModel)
    {
        parent::__construct();
        $this->userModel = $userModel;
    }

    public function verifyUserCredential($email, $password) 
    {
        $user = $this->userModel->where("email", $email)->findAll();

        if (!$user) {
            throw new UnauthorizedError("Login failed. Email not found");
        }

        $hashedPassword = $user["password"];
        $match = password_verify($password, $hashedPassword);

        if (!$match) {
            throw new UnauthorizedError("Login failed. Password is wrong");
        }

        return $user["id"];
    }

    public function verifyNewUsername($name)
    {
        $user = $this->userModel->where("name", $name)->findAll();

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
        
        $this->userModel->insert($data);
        
        return $id;
    }

    public function getAll()
    {
        $users = $this->userModel->findAll();

        if (empty($users)) {
            throw new NotFoundError("Users not found");
        }

        return $users;
    }

    public function getById($id)
    {
        $user = $this->userModel->find($id);

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