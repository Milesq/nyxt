<?php
abstract class Controller {
    private $data = [];
    abstract public function handle();

    public function __construct(array $slug) {
        $this->data = $slug;
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
            && (require('./utils/is_uppercased.php'))($name[3])
        ) {
            $param_name = substr($name, 3);
            $this->data[lcFirst($param_name)] = $args[0] ?? false;
            return $this;
        }

        throw new Exception("Method $name, doesnt exists on Controller");
    }

    protected function render(string $name, array $args = []) {
        // relative to require vendor/autoload
        $loader = new \Twig\Loader\FilesystemLoader('./templates');
        $twig = new \Twig\Environment($loader, [
            'cache' => '.cache',
        ]);

        echo $twig->render("$name.html", $this->data + $args + ['ByMethod' => 43]);
    }
}
