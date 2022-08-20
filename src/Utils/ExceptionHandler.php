<?php

namespace Trulyao\PhpJwt\Utils;

use Exception;
use PDOException;
use Trulyao\PhpJwt\Utils\CustomException as CustomException;

class ExceptionHandler {
    public static function filterException(Exception|CustomException|PDOException $e): CustomException {
        if($e instanceof CustomException) {
            return $e;
        }
        return new CustomException("An error occurred", 500);
    }
}
