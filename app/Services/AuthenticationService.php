<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Models\Authentication;

class AuthenticationService extends BaseService
{
    protected $model;

    public function __construct(Authentication $authModel)
    {
        $this->model = $authModel;
    }

    public function addRefreshToken($token)
    {
        $this->model->insert($token);
    }

    public function verifyRefreshToken($token)
    {
        $refreshToken = $this->model->where("token", $token)->findAll();

        if (!$refreshToken) {
            throw new BadRequestError("Invalid refresh token");
        }
    }

    public function deleteRefreshToken($token)
    {
        $this->model->where("token", $token)->delete();
    }
}