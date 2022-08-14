<?php

namespace Trulyao\PhpJwt\Services;

use PDO;
use PDOStatement;

class Connection
{
    private $pdo;
    private $dsn;

    public function __construct()
    {
        [$host, $port, $user, $pass, $db] = [
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_PORT'],
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_PASS'],
            $_ENV['MYSQL_DATABASE']
        ];
        $this->dsn = "mysql:host={$host};port={$port};dbname={$db}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($this->dsn, $user, $pass, $options);
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    public function getDSN(): string
    {
        return $this->dsn;
    }

    public function query_data(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function select_data(string $sql, array $params = []): array
    {
        $stmt = $this->query_data($sql, $params);
        return $stmt->fetchAll();
    }

    public function lastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }
}