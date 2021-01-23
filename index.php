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
        $matches = [];
        preg_match_all('/\{([^\}]+)\}/', $entry[1], $matches);

        $h = new Handler(array_combine($matches[1], $args));
        $h->handle();
    });
}

$router->run();
