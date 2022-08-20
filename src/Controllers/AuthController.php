<?php

namespace Trulyao\PhpJwt\Controllers;

use Exception;
use Trulyao\PhpJwt\Models\User;
use Trulyao\PhpJwt\Utils\CustomException;
use Trulyao\PhpJwt\Utils\ResponseHandler as ResponseHandler;
use Trulyao\PhpRouter\HTTP\Response as Response;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpJwt\Services\Connection as Connection;
use Trulyao\PhpJwt\Services\AuthService as AuthService;

class AuthController
{

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

            if(strlen($password) < 6) {
                throw new CustomException("Password must be at least 6 characters long!", 400);
            }

            if ($password !== $confirm_password) {
                throw new CustomException("Passwords do not match", 400);
            }

            $email = strtolower($email);

            $check = $this->pdo->query_data("SELECT * FROM `users` WHERE email = :email", ["email" => $email])->rowCount();

            if ($check > 0) {
                throw new CustomException("Account already exists", 400);
            }

            $password = password_hash($password, PASSWORD_DEFAULT);

            $user = new User();
            $user->name = $full_name;
            $user->email = $email;
            $user->password = $password;
            $user->save();

            return ResponseHandler::success($response, "User created successfully", 201);
        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }

    public function loginUser(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            if (empty($email) || empty($password)) {
                throw new CustomException("Please fill all the fields", 400);
            }

            $email = strtolower($email);

            $check = $this->pdo->query_data("SELECT * FROM `users` WHERE email = :email", ["email" => $email])->rowCount();

            if ($check < 1) {
                throw new CustomException("Invalid credentials!", 400);
            }

            $user = User::findByEmail($email);

            if (!password_verify($password, $user->password)) {
                throw new CustomException("Invalid credentials!", 400);
            }

            $generatedJWT = AuthService::generateToken($user->id, $this->getUrl());

            $response_data = [
                "email" => $user->email,
                "access_token" => $generatedJWT["token"],
                "valid_for" => $generatedJWT["ttl"],
            ];

            return ResponseHandler::success($response, "Welcome back!", 200, $response_data);

        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }

    }

    protected function getUrl(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }

}