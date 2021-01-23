<?php
require_once './vendor/autoload.php';
define("CONTROLLERS_DIR", "controllers");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router = new \Bramus\Router\Router();

$router->set404(function() {
    header('Location: /');
});

foreach (scan_controllers(CONTROLLERS_DIR) as $entry) {
    print_r($entry);
}

// $router->run();
function scan_controllers(string $dir, string $dirname = '/') {
    $controllers = [];

    foreach (scandir($dir) as $entity_name) {
        if (in_array($entity_name, ['.', '..'])) continue;

        $path = "$dir/$entity_name";

        if (is_dir($path)) {
            foreach (scan_controllers($path, $entity_name) as $controller) {
                [$controller_path, $controller_exp] = $controller;
                $controllers[] = [$controller_path, "/$entity_name$controller_exp"];
            }
            continue;
        }

        if (!str_ends_with($entity_name, '.php')) continue;

        $controller_name = substr($entity_name, 0, -4); // remove '.php'
        $controller_data = [$path];

        if (str_starts_with($entity_name, '_')) {
            $controller_name = substr($controller_name, 1);

            $controller_data[] = "/{{$controller_name}}";
        } elseif ($entity_name === 'index.php') {
            $controller_data[] = '/';
        } else {
            $controller_data[] = "/$controller_name";
        }

        $controllers[] = $controller_data;
    }

    return $controllers;
}
