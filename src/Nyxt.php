<?php
namespace Nyxt;

final class Base {
    public $router;

    function __construct() {
        $this->router = new \Bramus\Router\Router();

        $this->router->set404(function() {
            $file404 = './template/[404].html';

            if (file_exists($file404)) {
                $loader = new \Twig\Loader\ArrayLoader([
                    '404' => file_get_contents($file404),
                ]);
                $twig = new \Twig\Environment($loader);
                $twig->render('404', [ 'why' => 'not found' ]);
            } else {
                echo '404 - Not Found';
            }
        });

        $this->router->before('GET', '/(.*)', function($path) {
            $path = "./public/$path";
            if (is_file($path)) {
                echo file_get_contents($path);
                exit();
            }
        });

        foreach (scan_controllers('controllers') as $entry) {
            $this->router->all($entry[1], function(...$args) use ($entry) {
                include $entry[0];
                $matches = [];
                preg_match_all('/\{([^\}]+)\}/', $entry[1], $matches);

                $h = new \Handler(array_combine($matches[1], $args));
                $h->handle();
            });
        }
    }

    function run() {
        $this->router->run();
    }
}
