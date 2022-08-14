<?php

namespace Trulyao\PhpJwt\Controllers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Trulyao\PhpRouter\Helper\Response as Response;
use Trulyao\PhpRouter\Helper\Request as Request;
use Trulyao\PhpJwt\Services\Connection as Connection;

class AuthController
{

    private $pdo;
    private $conn;

    public function __construct()
    {
        $this->pdo = new Connection();
        $this->conn = $this->pdo->getPDO();
    }

    public function sign_up(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
                return $response->status(400)->json([
                    "success" => false,
                    "message" => "Please fill all fields"
                ]);
            }

            $name = explode(" ", $full_name);

            [$first_name, $last_name] = $name + [null, null];

            if (empty($first_name) || empty($last_name)) {
                return $response->status(400)->json([
                    "success" => false,
                    "message" => "First name and last name are required"
                ]);
            }

            if ($password !== $confirm_password) {
                return $response->status(400)->json([
                    "success" => false,
                    "message" => "Passwords do not match"
                ]);
            }

            $email = strtolower($email);

            $check = $this->pdo->query_data("SELECT * FROM `users` WHERE email = :email", ["email" => $email])->rowCount();

            if ($check > 0) {
                return $response->status(400)->json([
                    "success" => false,
                    "message" => "Email already exists"
                ]);
            }

            $password = password_hash($password, PASSWORD_DEFAULT);

            $user = [
                "name" => $full_name,
                "email" => $email,
                "password" => $password,
            ];

            $this->pdo->query_data("INSERT INTO `users` (name, email, password) VALUES (:name, :email, :password)", $user);

            return $response->status(201)->json(["success" => true, "message" => "User created successfully"]);
        } catch (Exception $e) {
            return $response->status(500)->json([
                "success" => false,
                "message" => "Server error!"
            ]);
        }
    }

    public function sign_in(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            if (empty($email) || empty($password)) {
                return $response->status(400)->json([
                    "success" => false,
                    "message" => "Please fill all fields"
                ]);
            }

            $email = strtolower($email);

            $check = $this->pdo->query_data("SELECT * FROM `users` WHERE email = :email", ["email" => $email])->rowCount();

            if ($check < 1) {
                return $response->status(400)->json([
                    "success" => false,
                    "message" => "Invalid credentials"
                ]);
            }

            $user = $this->pdo->select_data("SELECT * FROM `users` WHERE email = :email", ["email" => $email])[0];

            if (!password_verify($password, $user->password)) {
                return $response->status(400)->json([
                    "success" => false,
                    "message" => "Invalid credentials"
                ]);
            }

            $token = $this->generate_token($user->id);

            return $response->status(200)->json([
                "success" => true,
                "message" => "User logged in successfully",
                "data" => [
                    "access_token" => $token
                ]
            ]);

        } catch (Exception $e) {
            return $response->status(500)->json([
                "success" => false,
                "message" => "Server error!"
            ]);
        }

    }

    protected function generate_token($id): string
    {
        $payload = [
            "iss" => $this->get_url(),
            "aud" => $this->get_url(),
            "iat" => time(),
            "nbf" => time(),
            "data" => [
                "id" => $id,
            ]
        ];

        $key = $_ENV["JWT_SECRET"];

        return JWT::encode($payload, $key, 'HS256');
    }

    protected function decode_token($token): ?\stdClass
    {
        $key = $_ENV["JWT_SECRET"];
        return JWT::decode($token, new Key($key, 'HS256')) ?? null;
    }

    protected function get_url(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }

}