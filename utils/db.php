<?php
return function() {
    $host = $_ENV['DB_HOST'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $name = $_ENV['DB_NAME'];

    return new PDO("mysql:host=$host;dbname=$name", $user, $pass);
};
