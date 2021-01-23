<?php
require_once './vendor/autoload.php';
define("CONTROLLERS_DIR", "controllers");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router = new \Bramus\Router\Router();

$router->set404(function() {
    header('Location: /');
});

$router->run();
