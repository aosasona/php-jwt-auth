<?php

require("vendor/autoload.php");

use Trulyao\PhpRouter\Router as Router;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$router = new Router(__DIR__ . "/src", "v1");

$router->get("/phpmyadmin", function($request, $response) {
    return $response->redirect("http://localhost:2083");
});

$router->get("/:name", function($request, $response) {
    return $response->send("Hello, {$request->params("name")}");
});

$router->serve();