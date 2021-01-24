<?php
namespace Nyxt;

final class Base {
    public $router;

    function __construct() {
        $this->router = new \Bramus\Router\Router();

        $this->router->set404(function() {
            echo '404';
        });

        $this->router->before('GET', '/(.*)', function($path) {
            var_dump(is_file("./public/$path"));
            exit;
        });

        foreach (scan_controllers('controllers') as $entry) {
            $this->router->all($entry[1], function(...$args) use ($entry) {
                include $entry[0];
                $matches = [];
                preg_match_all('/\{([^\}]+)\}/', $entry[1], $matches);

                $h = new Handler(array_combine($matches[1], $args));
                $h->handle();
            });
        }
    }

    function run() {
        $this->router->run();
    }
}