<?php
abstract class Controller {
    private $slug = [];
    abstract public function handle();

    public function __construct(array $slug) {
        $this->slug = $slug;
    }

    public function __get(string $name) {
        return $this->slug[$name];
    }

    public function __set(string $name, mixed $value) {
        $this->slug[$name] = $value;
    }

    public function __call(string $name, array $args): Self {
        if (
            str_starts_with($name, 'set')
            && isset($name[3])
            && is_uppercased($name[3])
        ) {
            $param_name = substr($name, 3);
            $this->slug[lcFirst($param_name)] = $args[0] ?? false;
            return $this;
        }

        throw new Exception("Method $name, doesnt exists on Controller");
    }

    protected function render(string $name, array $args = []) {
        // relative to require vendor/autoload
        $loader = new \Twig\Loader\FilesystemLoader('./templates');
        $twig = new \Twig\Environment($loader);

        echo $twig->render("$name.html", $this->slug + $args + ['ByMethod' => 43]);
    }
}

function is_uppercased(string $s): bool {
    return strtoupper($s) == $s;
}
