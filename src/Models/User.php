<?php

namespace Trulyao\PhpJwt\Models;

use Exception;
use PDO;
use PDOException;
use stdClass;
use Trulyao\PhpJwt\Utils\CustomException as CustomException;
use Trulyao\PhpJwt\Services\Connection as Connection;


class User
{
    public string $name;
    public string $email;
    public string $password;

    /**
     * @throws CustomException
     */
    public function save(): stdClass
    {
        if (empty($this->name) || empty($this->email) || empty($this->password)) {
            throw new CustomException("All fields are required!", 400);
        }
        $pdo = new Connection();
        $data = [
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password
        ];
        $pdo->query_data("INSERT INTO `users` (`name`, `email`, `password`) VALUES (:name, :email, :password)", [
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password
        ]);
        return (object)$data;
    }

    /**
     * @throws CustomException
     */
    public static function findOne(int $id): stdClass
    {
        if (!$id || !is_numeric($id)) {
            throw new CustomException("Invalid user ID", 401);
        }
        $pdo = new Connection();
        $stmt = $pdo->query_data("SELECT * FROM `users` WHERE id = :id", [
            "id" => $id
        ]);
        return $stmt->fetch();
    }

    /**
     * @throws CustomException
     */
    public static function findByEmail(string $email)
    {
        if (empty($email)) {
            throw new CustomException("Email is required!", 400);
        }
        $pdo = new Connection();
        $stmt = $pdo->query_data("SELECT * FROM `users` WHERE email = :email", [
            "email" => $email
        ]);
        return $stmt->fetch();
    }
}