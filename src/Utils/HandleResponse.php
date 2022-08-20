<?php

namespace Trulyao\PhpJwt\Utils;

use \Exception;
use \Trulyao\PhpJwt\Utils\CustomException as CustomException;
use Trulyao\PhpRouter\HTTP\Response as Response;

class HandleResponse
{

    public static function success(Response $response, string $message, array $data = [], int $code = 200): Response
    {
        $response->status($code);
        return $response->send([
            "status" => "success",
            "message" => $message,
            "data" => $data
        ]);
    }

    public static function error(Response $response, CustomException | Exception $e)
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