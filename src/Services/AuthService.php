<?php

namespace Trulyao\PhpJwt\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use PDOStatement;
use Exception;

class AuthService {
    public static function generateToken(int $id, string $url): array
    {
        $exp_multiplier = 60 * 60;
        $payload = [
            "iss" => $url,
            "aud" => $url,
            "iat" => time(),
            "exp" => time() + $exp_multiplier,
            "data" => [
                "user_id" => $id,
            ]
        ];

        $key = $_ENV["JWT_SECRET"];

        $ttl = $exp_multiplier/60;

        return ["token" => JWT::encode($payload, $key, 'HS256'), "ttl" => "{$ttl}m"];
    }

    public static function decodeToken(string $token): object | null
    {
        try{
            $key = $_ENV["JWT_SECRET"];
            return JWT::decode($token, new Key($key, 'HS256')) ?? null;
        } catch (Exception $e) {
            return null;
        }

    }

    public static function getUserById(int $id): object | null
    {
        $pdo = new Connection();
        $stmt = $pdo->query_data("SELECT `id`, `name`, `email` FROM `users` WHERE id = :id", ["id" => $id]);
        return $stmt->fetch();
    }
}
