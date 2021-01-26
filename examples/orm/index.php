<?php
require_once '../../vendor/autoload.php';
require_once '../../src/Utils/is_uppercased.php';
require_once '../../src/Utils/array_flatten.php';
require_once '../../src/Model.php';
require_once '../../src/Controller.php';
require_once '../../src/Nyxt.php';

$framework = new \Nyxt\Base(function() {
    return new PDO("mysql:host=localhost;dbname=bikes", "root", "test");
});
$framework->run();
