<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Exception;

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
        try {
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
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function refresh()
    {
        try {
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
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function logout()
    {
        try {
            $token = validateRequest("token")["refreshToken"];

            $this->authService->verifyRefreshToken($token);
            $this->authService->deleteRefreshToken($token);

            return $this->respond([
                "status" => "success",
                "message" => "Refresh token deleted",
            ], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}
