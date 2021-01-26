<?php
namespace Nyxt;

use ICanBoogie\Inflector;
use \ClanCats\Hydrahon\Query\Sql\Table;

abstract class Model {
    var array $__columns = [NULL];

    public function __construct(protected Table $table) {
        if ($this->__columns[0] === NULL) {
            throw new \Exception('__columns is not declared');
        }
    }

    public function select() {
        return $this->table->select($this->__columns);
    }

    public function all(): array {
        return $this->table->select()->get();
    }

    public function create(...$args) {
        $values = [];

        foreach ($args as $key => $arg) {
            $name = $this->__columns[$key];
            $values[$name] = $arg;
        }

        return $this->table->insert([$values])->execute();
    }

    public function findBy(...$args) {
        return $this->table->select()->where(...$args);
    }

    public function __call($name, $args) {
        if (!str_starts_with($name, 'findBy')) {
            throw new \Exception("Method $name doesn't exist on type Model");
        }

        $name = lcfirst(substr($name, 6));

        return $this->findBy($name, ...$args);
    }

    static function scan_dir(string $dir) {
        $inflector = Inflector::get('en');
        foreach (scandir($dir, SCANDIR_SORT_DESCENDING) as $entity_name) {
            $path = "$dir/$entity_name";
            if (
                in_array($entity_name, ['.', '..'])
                || !is_file($path)
                || !str_ends_with($entity_name, '.php')
            ) continue;

            $entity_name = substr($entity_name, 0, -4);

            $result[] = [
                "\\$entity_name",
                $inflector->pluralize(lcfirst($entity_name)),
                $path
            ];
        }

        return $result;
    }
}
