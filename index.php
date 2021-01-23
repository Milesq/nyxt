<?php
require_once './vendor/autoload.php';
require_once './lib/scan-controllers.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router = new \Bramus\Router\Router();

$router->set404(function() {
    echo '404';
});

foreach (scan_controllers('controllers') as $entry) {
    $router->all($entry[1], function() use ($entry) {
        echo $entry[0];
    });
}

$router->run();
