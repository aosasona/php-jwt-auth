<?php

namespace Trulyao\PhpJwt\Utils;

use \Exception;
use \Trulyao\PhpJwt\Utils\CustomException as CustomException;
use Trulyao\PhpRouter\HTTP\Response as Response;

class HandleResponse
{

    public static function success(Response $response, string $message, int $code = 200, array $data = []): Response
    {
        return $response->status($code)->send([
            "status" => "success",
            "code" => $code,
            "message" => $message,
            "data" => $data
        ]);
    }

    public static function error(Response $response, CustomException | Exception $e): Response
    {
        $code = 500;
        $message = "An error occurred";
        $data = null;

        if($e instanceof CustomException) {
            $code = $e->getCode();
            $message = $e->getMessage();
            $data = $e->data;
        }

        return $response->status($code)->send([
            "success" => false,
            "code" => $code,
            "data" => $data,
            "message" => $message
        ]);
    }
}