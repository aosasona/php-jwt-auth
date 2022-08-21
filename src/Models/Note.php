<?php

namespace Trulyao\PhpJwt\Models;

use stdClass;
use Trulyao\PhpJwt\Utils\CustomException as CustomException;
use Trulyao\PhpJwt\Services\Connection as Connection;

class Note
{
    public string $title;
    public string $content;
    public int $user_id;

    /**
     * @throws CustomException
     */
    public function save(): stdClass|null
    {
        if (empty($this->title) || empty($this->content)) {
            throw new CustomException("All fields are required!", 400);
        }

        if (!$this->user_id || !is_numeric($this->user_id)) {
            throw new CustomException("Invalid user", 401);
        }

        $pdo = new Connection();
        $data = [
            "title" => $this->title,
            "content" => $this->content,
            "user_id" => $this->user_id
        ];
        $stmt = $pdo->query_data("INSERT INTO `notes` (`title`, `content`, `user_id`) VALUES (:title, :content, :user_id)", $data);
        $affected_rows = $stmt->rowCount();
        if ($affected_rows > 0) {
            return (object)$data;
        } else {
            throw new CustomException("Something went wrong", 500);
        }
    }

    /**
     * @throws CustomException
     */
    public static function findOne(int $id): bool|stdClass
    {
        if (!$id || !is_numeric($id)) {
            throw new CustomException("Invalid note ID", 401);
        }
        $pdo = new Connection();
        $stmt = $pdo->query_data("SELECT * FROM `notes` WHERE id = :id", [
            "id" => $id,
        ]);
        return $stmt->fetch();
    }

    /**
     * @throws CustomException
     */
    public static function findMany(int $user_id): bool|array
    {
        if (!$user_id || !is_numeric($user_id)) {
            throw new CustomException("Invalid user ID", 401);
        }
        $pdo = new Connection();
        $stmt = $pdo->query_data("SELECT * FROM `notes` WHERE user_id = :user_id", [
            "user_id" => $user_id
        ]);
        return $stmt->fetchAll();
    }

    /**
     * @throws CustomException
     */
    public static function deleteOne(int $id): bool
    {
        if (!$id || !is_numeric($id)) {
            throw new CustomException("Invalid note ID", 401);
        }
        $pdo = new Connection();
        $stmt = $pdo->query_data("DELETE FROM `notes` WHERE id = :id", [
            "id" => $id
        ]);
        return $stmt->rowCount();
    }
}