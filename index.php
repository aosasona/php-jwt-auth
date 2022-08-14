<?php

require("vendor/autoload.php");

use Trulyao\PhpRouter\Router as Router;
use Trulyao\PhpJwt\Controllers\AuthController as AuthController;

//  Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Controllers
$authController = new AuthController();

// Set up router
$router = new Router(__DIR__ . "/src", "v1");

$router->post("/auth/login", [$authController, "sign_in"]);

$router->post("/auth/signup", [$authController, "sign_up"]);

$router->get("/phpmyadmin", function ($request, $response) {
    return $response->redirect("http://localhost:2083");
});

$router->serve();
