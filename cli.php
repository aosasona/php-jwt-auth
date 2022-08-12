#!/usr/local/bin/php

<?php

require("vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

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
        $dsn = "postgres://{$_ENV['POSTGRES_USER']}:{$_ENV['POSTGRES_PASSWORD']}@{$_ENV['POSTGRES_HOST']}:{$_ENV['POSTGRES_PORT']}/{$_ENV['POSTGRES_DATABASE']}";
        $pdo = new PDO($dsn);
        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
        $pdo->setAttribute(
            PDO::ATTR_EMULATE_PREPARES,
            false
        );
        $pdo->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_OBJ
        );

        $pdo->exec("DROP SCHEMA public CASCADE;");
        $pdo->exec("CREATE SCHEMA public;");
        $pdo->exec("GRANT ALL ON SCHEMA public TO postgres;");
        $pdo->exec("GRANT ALL ON SCHEMA public TO public;");

        $sql = `
        CREATE TABLE public.users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT NOW(),
            updated_at TIMESTAMP NOT NULL DEFAULT NOW()
        );
        `;
        $pdo->exec($sql);
        echo "Migration complete\n";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

?>