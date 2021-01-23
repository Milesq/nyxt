<?php
require_once './vendor/autoload.php';
require_once './lib/scan-controllers.php';
require_once './lib/Controller.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router = new \Bramus\Router\Router();

$router->set404(function() {
    echo '404';
});

foreach (scan_controllers('controllers') as $entry) {
    $router->all($entry[1], function(...$args) use ($entry) {
        include $entry[0];
        print_r($args);
        print_r($entry[0]);
    });
}

$router->run();
