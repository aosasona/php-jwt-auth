<?php

namespace Trulyao\PhpJwt\Utils;

use \Exception;

class CustomException extends Exception
{
    public string $name;
    public mixed $data;

    public function __construct(string $message, int $code, mixed $data = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->name = "CustomException";
        $this->data = $data;
    }
}