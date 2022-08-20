<?php

require("vendor/autoload.php");

use Trulyao\PhpRouter\Router as Router;
use Trulyao\PhpJwt\Controllers\AuthController as AuthController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$router = new Router(__DIR__ . "/src", "v1");

$router->allowed(["application/json", "application/x-www-form-urlencoded", "multipart/form-data", "multipart/form-data; boundary=X-INSOMNIA-BOUNDARY"]);

$router->post("/auth/login", [new AuthController(), "loginUser"]);

$router->post("/auth/signup", [new AuthController(), "createUser"]);

$router->get("/phpmyadmin", function ($request, $response) {
    return $response->redirect("http://localhost:2083");
});

$router->serve();
