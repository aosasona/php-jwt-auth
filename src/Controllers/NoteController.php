<?php

namespace Trulyao\PhpJwt\Controllers;

use Exception;
use PDO;
use Trulyao\PhpJwt\Utils\CustomException;
use Trulyao\PhpRouter\HTTP\Response as Response;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpJwt\Utils\HandleResponse as HandleResponse;
use Trulyao\PhpJwt\Services\Connection as Connection;

class NoteController {

    private PDO $conn;
    private Connection $pdo;

    public function __construct() {
        $this->pdo = new Connection();
        $this->conn = $this->pdo->getPDO();
    }

    public function createNote(Request $request, Response $response) {
        try {

        } catch (CustomException|Exception $e) {
            return HandleResponse::error($response, $e);
        }
    }
}