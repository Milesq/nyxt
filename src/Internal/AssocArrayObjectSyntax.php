<?php
namespace Nyxt\Internal;

class AssocArrayObjectSyntax {
    protected $data = [];
    public function __construct(array $init) {
        $this->data = $init;
    }

    public function __get(string $name) {
        return $this->data[$name];
    }

    public function __set(string $name, mixed $value) {
        $this->data[$name] = $value;
    }

    public function reset(): Self {
        $this->data = [];
        return $this;
    }

    public function unset($name): Self {
        unset($this->data[$name]);
        return $this;
    }

    public function getData() {
        return $this->data;
    }

    public function __call(string $name, array $args): Self {
        if (
            str_starts_with($name, 'set')
            && isset($name[3])
            && \Nyxt\Utils\is_uppercased($name[3])
        ) {
            $param_name = substr($name, 3);
            $this->data[lcFirst($param_name)] = $args[0] ?? false;
            return $this;
        }

        throw new \Exception("Method $name, doesnt exists on Controller");
    }
}
