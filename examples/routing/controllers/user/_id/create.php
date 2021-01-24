<?php
class Handler extends \Nyxt\Controller {
    public function handle() {
        echo 'URL is: /user/{whatever}/create'.PHP_EOL;
        echo "ID is: {$this->id}";
    }
}
