<?php
namespace Nyxt;

use \Rakit\Validation\Validator;

abstract class Controller extends Internal\AssocArrayObjectSyntax {
    // private $orm_obj = NULL;
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

    // protected function getOrm() {
    //     if ($this->orm_obj === NULL) {
    //         $pdo = $this->get_pdo_fn;
    //         if ($pdo === NULL) {
    //             throw new \Exception('PDO connection getter was not declared');
    //         }

    //         $this->orm_obj = new \ClanCats\Hydrahon\Builder('mysql', function($query, $queryString, $queryParameters) use($pdo) {
    //             echo $queryString;
    //             // $statement = $pdo->prepare($queryString);
    //             // $statement->execute($queryParameters);

    //             if ($query instanceof \ClanCats\Hydrahon\Query\Sql\FetchableInterface) {
    //                 // return $statement->fetchAll();
    //                 return ['e' => 2];
    //             }
    //         });
    //     }

    //     return $this->orm_obj;
    // }

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
}
