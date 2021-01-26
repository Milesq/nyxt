<?php
namespace Nyxt;

use \ClanCats\Hydrahon\Builder as Hydrahon;
use \ClanCats\Hydrahon\Query\Sql\FetchableInterface;

final class Base {
    public $router;
    protected $orm;

    protected function setupORM($get_pdo_fn, &$handler) {
        if ($get_pdo_fn === NULL) throw new \Exception('connection getter not specified');

        $pdo = $get_pdo_fn();
        $orm = new Hydrahon('mysql', function($query, $queryString, $queryParameters) use($pdo) {
            // $statement = $connection->prepare($queryString);
            // $statement->execute($queryParameters);
            echo $queryString;

            if ($query instanceof FetchableInterface) {
                // return $statement->fetchAll(\PDO::FETCH_ASSOC);
                return [];
            }
        });

        $models = Model::scan_dir('model');
        foreach ($models as [$className, $name, $path]) {
            include $path;

            $handler->$name = new $className($orm->table($name));
        }
    }

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
                header("Location: $path");
                exit();
            }
        });

        foreach (Controller::scan_dir('controllers') as $entry) {
            $this->router->all($entry[1], function(...$slugs) use ($entry, $get_pdo_fn) {
                include $entry[0];
                $matches = [];
                preg_match_all('/\{([^\}]+)\}/', $entry[1], $matches);

                $reflect = new \ReflectionClass(\Handler::class);
                $handler_args = Self::getNyxtArgs($reflect);

                $h = new \Handler(array_combine($matches[1], $slugs), $get_pdo_fn);

                if (in_array('orm', $handler_args, true))
                    $this->setupORM($get_pdo_fn, $h);

                $h->handle();
            });
        }
    }

    function run() {
        $this->router->run();
    }

    protected static function getNyxtArgs($reflect) {
        $args = [];

        foreach ($reflect->getAttributes() as $attr) {
            if ($attr->getName() === 'nyxt') {
                $args[] = $attr->getArguments();
            }
        }

        return Utils\array_flatten($args);
    }
}
