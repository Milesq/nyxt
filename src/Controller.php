<?php
namespace Nyxt;

use \Rakit\Validation\Validator;

abstract class Controller {
    private $data = [];
    abstract public function handle();

    /**
     * @return bool|string validate returns error (string) or true
     *  If returns false value, default error will be displayed
     */
    protected function validate(Validator $v): bool|string|array {
        return true;
    }

    public function __construct(array $slug) {
        $this->data = $slug;
        $msg = $this->validate(new Validator);

        $msg_is_string = gettype($msg) === 'string';

        if (!$msg || $msg_is_string) {
            if ($msg_is_string) echo $msg;
            else {
                if (!$this->try_render('[error]')) {
                    echo 'Validation error has occured';
                }
            }

            die();
        }
    }

    public function __get(string $name) {
        return $this->slug[$name];
    }

    public function __set(string $name, mixed $value) {
        $this->data[$name] = $value;
    }

    public function __call(string $name, array $args): Self {
        if (
            str_starts_with($name, 'set')
            && isset($name[3])
            && Utils\is_uppercased($name[3])
        ) {
            $param_name = substr($name, 3);
            $this->data[lcFirst($param_name)] = $args[0] ?? false;
            return $this;
        }

        throw new Exception("Method $name, doesnt exists on Controller");
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

        echo $twig->render("$name.html", $this->data + $args);
    }
}
