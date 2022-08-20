<?php

namespace Trulyao\PhpJwt\Controllers;

use Exception;
use Trulyao\PhpJwt\Utils\CustomException;
use Trulyao\PhpJwt\Utils\HandleException;
use Trulyao\PhpJwt\Utils\HandleResponse;
use Trulyao\PhpRouter\HTTP\Response as Response;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpJwt\Services\Connection as Connection;
use Trulyao\PhpJwt\Services\AuthService as AuthService;

class AuthController
{

    private $pdo;
    private $conn;

    public function __construct()
    {
        $this->pdo = new Connection();
        $this->conn = $this->pdo->getPDO();
    }

    public function createUser(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
                throw new CustomException("Please fill all the fields", 400);
            }

            $name = explode(" ", $full_name);

            [$first_name, $last_name] = $name + [null, null];

            if (empty($first_name) || empty($last_name)) {
                throw new CustomException("Full name is required!", 400);
            }

            if ($password !== $confirm_password) {
                throw new CustomException("Passwords do not match", 400);
            }

            $email = strtolower($email);

            $check = $this->pdo->query_data("SELECT * FROM `users` WHERE email = :email", ["email" => $email])->rowCount();

            if ($check > 0) {
                throw new CustomException("Email already exists", 400);
            }

            $password = password_hash($password, PASSWORD_DEFAULT);

            $user = [
                "name" => $full_name,
                "email" => $email,
                "password" => $password,
            ];

            $this->pdo->query_data("INSERT INTO `users` (name, email, password) VALUES (:name, :email, :password)", $user);

            return $response->status(201)->send(["success" => true, "message" => "User created successfully"]);
        } catch (CustomException $e) {
            return HandleResponse::error($response, $e);
        }
    }

    public function loginUser(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            if (empty($email) || empty($password)) {
                return $response->status(400)->send([
                    "success" => false,
                    "message" => "Please fill all fields"
                ]);
            }

            $email = strtolower($email);

            $check = $this->pdo->query_data("SELECT * FROM `users` WHERE email = :email", ["email" => $email])->rowCount();

            if ($check < 1) {
                return $response->status(400)->send([
                    "success" => false,
                    "message" => "Invalid credentials"
                ]);
            }

            $user = $this->pdo->select_data("SELECT * FROM `users` WHERE email = :email", ["email" => $email])[0];

            if (!password_verify($password, $user->password)) {
                return $response->status(400)->send([
                    "success" => false,
                    "message" => "Invalid credentials"
                ]);
            }

            $token = AuthService::generateToken($user->id, $this->getUrl());

            return $response->status(200)->send([
                "success" => true,
                "message" => "User logged in successfully",
                "data" => [
                    "access_token" => $token
                ]
            ]);

        } catch (Exception $e) {
            return $response->status(500)->send([
                "success" => false,
                "message" => "Server error!"
            ]);
        }

    }

    protected function getUrl(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }

}