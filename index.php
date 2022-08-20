<?php

error_reporting(0);

require("vendor/autoload.php");

use Trulyao\PhpRouter\Router as Router;
use Trulyao\PhpJwt\Middleware\AuthMiddleware as AuthMiddleware;
use Trulyao\PhpJwt\Controllers\AuthController as AuthController;
use Trulyao\PhpJwt\Controllers\NoteController as NoteController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$authMiddleware = [AuthMiddleware::class, "authorizeUser"];

$router = new Router(__DIR__ . "/src", "v1");

$router->allowed(["application/json", "application/x-www-form-urlencoded", "multipart/form-data"]);

$router->post("/auth/login", [AuthController::class, "loginUser"]);

$router->post("/auth/signup", [AuthController::class, "createUser"]);

$router->get("/notes", $authMiddleware, [NoteController::class, "getNotes"]);

$router->get("/notes/:id", $authMiddleware, [NoteController::class, "getNote"]);

$router->post("/notes", $authMiddleware, [NoteController::class, "createNote"]);

$router->delete("/notes/:id", $authMiddleware, [NoteController::class, "deleteNote"]);

$router->get("/phpmyadmin", function ($request, $response) {
    return $response->redirect("http://localhost:2083");
});

$router->serve();
