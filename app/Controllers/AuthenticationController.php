<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AuthenticationController extends BaseController
{
    protected $authService;
    protected $userService;

    public function __construct()
    {
        $this->authService = service("authenticationService");
        $this->userService = service("userService");
    }
    
    public function login()
    {
        $loginData = validateRequest("login");

        $id = $this->userService->verifyUserCredentials($loginData["email"], $loginData["password"]);

        $accessToken = generateAccessToken($id);
        $refreshToken = generateRefreshToken($id);

        $this->authService->addRefreshToken(["token" => $refreshToken]);

        return $this->respond([
            "status" => "success",
            "data" => [
                "accessToken" => $accessToken,
                "refreshToken" => $refreshToken
            ]
        ], 201);
    }

    public function refresh()
    {
        $token = validateRequest("token")["refreshToken"];

        $id = verifyToken($token, "REFRESH_TOKEN_KEY");
        $this->authService->verifyRefreshToken($token);

        $accessToken = generateAccessToken($id);

        return $this->respond([
            "status" => "success",
            "message" => "Access token has been refreshed",
            "data" => [
                "accessToken" => $accessToken,
            ]
        ], 200);
    }

    public function logout()
    {
        $token = validateRequest("token")["refreshToken"];

        $this->authService->verifyRefreshToken($token);
        $this->authService->deleteRefreshToken($token);

        return $this->respond([
            "status" => "success",
            "message" => "Refresh token deleted",
        ], 200);
    }
}
