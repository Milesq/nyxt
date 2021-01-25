<?php
namespace Nyxt;

use \Rakit\Validation\Validator;

abstract class Controller extends Internal\AssocArrayObjectSyntax {
    abstract public function handle();

    /**
     * @return bool|string validate returns error (string) or true
     *  If returns false value, default error will be displayed
     */
    protected function validate(Validator $v): bool|string|array {
        return true;
    }

    public final function __construct(array $slug, private $get_pdo_fn = NULL) {
        $this->data = $slug;
        $msg = $this->validate(new Validator);

        $msg_is_string = gettype($msg) === 'string';
        $default_message = 'Validation error has occured';

        if (!$msg || $msg_is_string) {
            if ($msg_is_string) echo $msg;
            else {
                if (!$this->try_render('[error]', ['why' => $default_message])) {
                    echo $default_message;
                }
            }

            die();
        }
    }

    protected function try_render(string $name, array $args = []) {
        try {
            $this->render($name);
        } catch (\Twig\Error\LoaderError $err) {
            return false;
        }

        return true;
    }

    protected function render(string $name, array $args = []) {
        // relative to require vendor/autoload
        $loader = new \Twig\Loader\FilesystemLoader('./templates');
        $twig = new \Twig\Environment($loader, [
            'cache' => ($_ENV['NYXT_MODE'] ?? 0 === 'production')? '.cache' : false,
        ]);

        echo $twig->render("$name.html", $this->getData() + $args);
    }

    static function scan_dir(string $dir, string $dirname = '/') {
        $controllers = [];

        foreach (scandir($dir, SCANDIR_SORT_DESCENDING) as $entity_name) {
            if (in_array($entity_name, ['.', '..'])) continue;

            $path = "$dir/$entity_name";

            if (is_dir($path)) {
                foreach (Self::scan_dir($path, $entity_name) as $controller) {
                    [$controller_path, $controller_exp] = $controller;

                    if (str_starts_with($entity_name, '_')) {
                        $slug_name = substr($entity_name, 1);
                        $entity_name = "{{$slug_name}}";
                    }

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
}
