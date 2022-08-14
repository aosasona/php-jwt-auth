#!/usr/local/bin/php

<?php

require("vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require_once("src/Services/Connection.php");

if(strtolower(php_sapi_name()) !== "cli") {
    die("This script can only be run from the command line");
}

$main_arg = $argv[1];

switch ($main_arg) {
    case "migrate:fresh":
        migrate_dev();
        break;
    default:
        echo "Unknown command: $main_arg\n";
        break;
}

/**
 * Make a fresh migration
 */
function migrate_dev() {
    try {
        $connection = new Connection();
        $pdo = $connection->getPDO();


        echo "Migrating database...\n";


        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$_ENV['MYSQL_DATABASE']}`");

        $users_query = "CREATE TABLE IF NOT EXISTS `users` (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        );";
        $notes_query = "CREATE TABLE IF NOT EXISTS `notes` (
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        );";


        $queries = [$users_query, $notes_query];

        foreach ($queries as $query) {
            $pdo->exec($query);
        }

        echo "Migration complete\n";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

?>