<?php

require("vendor/autoload.php");

use Trulyao\PhpRouter\Router as Router;
use Trulyao\PhpJwt\Controllers\AuthController as AuthController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$router = new Router(__DIR__ . "/src", "v1");

$router->post("/auth/login", [new AuthController(), "sign_in"]);

$router->post("/auth/signup", [new AuthController(), "sign_up"]);

$router->get("/phpmyadmin", function($request, $response) {
    return $response->redirect("http://localhost:2083");
});

$router->serve();