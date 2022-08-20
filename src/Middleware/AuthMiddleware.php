<?php

namespace Trulyao\PhpJwt\Middleware;

use Exception;
use Trulyao\PhpJwt\Models\User;
use Trulyao\PhpJwt\Utils\CustomException as CustomException;
use Trulyao\PhpJwt\Utils\ResponseHandler;
use Trulyao\PhpRouter\HTTP\Response as Response;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpJwt\Services\Connection as Connection;
use Trulyao\PhpJwt\Services\AuthService as AuthService;

class AuthMiddleware {
    public static function authorizeUser(Request $request, Response $response){
        try {
            $headers = $request->headers();
            $auth_header = $headers["authorization"];

            if (empty($auth_header)) {
                throw new CustomException("Access token not provided", 401);
            }

            if(!str_starts_with($auth_header, "Bearer")) {
                throw new CustomException("Invalid access token", 400);
            }

            $token = explode(" ", $auth_header)[1];

            $decoded = AuthService::decodeToken($token);

            if (!$decoded) {
                throw new CustomException("Invalid or expired access token", 401);
            }

            $user_id = $decoded->data->user_id;

            if(!$user_id || !is_numeric($user_id)) {
                throw new CustomException("Invalid access token", 401);
            }

            $user = User::findOne($user_id);

            if(!$user) {
                throw new CustomException("Invalid access token", 401);
            }

            $request->append("user", $user);

        } catch (CustomException|Exception $e) {
            return ResponseHandler::error($response, $e);
        }
    }
}