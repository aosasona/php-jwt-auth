<?php

namespace Trulyao\PhpJwt\Controllers;

use Exception;
use Trulyao\PhpJwt\Models\User;
use Trulyao\PhpJwt\Utils\CustomException;
use Trulyao\PhpJwt\Utils\InputHandler;
use Trulyao\PhpJwt\Utils\ResponseHandler as ResponseHandler;
use Trulyao\PhpRouter\HTTP\Response as Response;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpJwt\Services\AuthService as AuthService;

class AuthController
{


    public static function createUser(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
                throw new CustomException("Please fill all the fields", 400);
            }
            $full_name = InputHandler::normalizeString($full_name);
            $email = InputHandler::normalizeString($email);
            $password = InputHandler::normalizeString($password);
            $confirm_password = InputHandler::normalizeString($confirm_password);
            $name = explode(" ", $full_name);

            [$first_name, $last_name] = $name + [null, null];

            if (empty($first_name) || empty($last_name)) {
                throw new CustomException("Full name is required!", 400);
            }

            if (strlen($password) < 6) {
                throw new CustomException("Password must be at least 6 characters long!", 400);
            }

            if ($password !== $confirm_password) {
                throw new CustomException("Passwords do not match", 400);
            }

            $email = strtolower($email);

            $user_exists = User::findByEmail($email);

            if ($user_exists) {
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

    public static function loginUser(Request $request, Response $response): Response
    {
        try {
            extract($request->body());

            if (empty($email) || empty($password)) {
                throw new CustomException("Please fill all the fields", 400);
            }

            $email = strtolower($email);
            $email = InputHandler::normalizeString($email);
            $password = InputHandler::normalizeString($password);

            $user = User::findByEmail($email);

            if (!$user) {
                throw new CustomException("Invalid credentials!", 400);
            }

            if (!password_verify($password, $user->password)) {
                throw new CustomException("Invalid credentials!", 400);
            }

            $generatedJWT = AuthService::generateToken($user->id, self::getUrl());

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

    protected static function getUrl(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }

}