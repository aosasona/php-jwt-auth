#!/usr/local/bin/php

<?php

require("vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require_once("src/services/Connection.php");

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
        echo "Migrating database...\n";

        $connection = new Connection();
        $pdo = $connection->getPDO();

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$_ENV['MYSQL_DATABASE']}`");

        $sql = `
        CREATE TABLE IF NOT EXISTS users (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        );
        `;
        $pdo->exec($sql);
        echo "Migration complete\n";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

?>