<?php

require("vendor/autoload.php");

use Trulyao\PhpRouter\Router as Router;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$router = new Router(__DIR__ . "/src", "v1");
