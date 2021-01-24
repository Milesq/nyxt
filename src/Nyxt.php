<?php
namespace Nyxt;

final class Base {
    public $router;

    function __construct($get_pdo_fn = NULL) {
        $this->router = new \Bramus\Router\Router();

        $this->router->set404(function() {
            $file404 = './public/404.html';
            $error_template = './templates/[error].html';

            if (file_exists($file404)) {
                echo file_get_contents($file404);
            } elseif (file_exists($error_template)) {
                $loader = new \Twig\Loader\ArrayLoader([
                    'error' => file_get_contents($error_template),
                ]);
                $twig = new \Twig\Environment($loader);

                echo $twig->render('error', ['why' => 'not found', 'errcode' => '404']);
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
            $this->router->all($entry[1], function(...$args) use ($entry, $get_pdo_fn) {
                include $entry[0];
                $matches = [];
                preg_match_all('/\{([^\}]+)\}/', $entry[1], $matches);

                $h = new \Handler(array_combine($matches[1], $args), $get_pdo_fn);
                $h->handle();
            });
        }
    }

    function run() {
        $this->router->run();
    }
}
