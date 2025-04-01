<?php

use App\Exceptions\BadRequestError;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists("generateAccessToken")) {
    function generateAccessToken($id) {
        $key = getenv("ACCESS_TOKEN_KEY");
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;

        $payload = [
            "iss" => base_url(),
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "sub" => $id
        ];

        return JWT::encode($payload, $key, "HS256");
    }
}

if (!function_exists("generateRefreshToken")) {
    function generateRefreshToken($id) {
        $key = getenv("REFRESH_TOKEN_KEY");
        $issuedAt = time();
        $expirationTime = $issuedAt + 604800; //7 days

        $payload = [
            "iss" => base_url(),
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "sub" => $id
        ];

        return JWT::encode($payload, $key, "HS256");
    }
}

if (!function_exists("verifyToken")) {
    function verifyToken($token, $secretKey) {
        $key = getenv($secretKey);
        
        try {
            return JWT::decode($token, new Key($key, "HS256"));
        } catch (Exception $e) {
            throw new BadRequestError("Invalid or expired token");
        }
    }
}