<?php

namespace Trulyao\PhpJwt\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use PDOStatement;
use Exception;

class AuthService {
    public static function generateToken(int $id, string $url): string
    {
        $payload = [
            "iss" => $url,
            "aud" => $url,
            "iat" => time(),
            "nbf" => time(),
            "data" => [
                "id" => $id,
            ]
        ];

        $key = $_ENV["JWT_SECRET"];

        return JWT::encode($payload, $key, 'HS256');
    }

    public static function decodeToken(string $token): array | null
    {

        $key = $_ENV["JWT_SECRET"];
        return (array) JWT::decode($token, new Key($key, 'HS256')) ?? null;
    }
}
