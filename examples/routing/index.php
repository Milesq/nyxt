<?php
require_once '../../vendor/autoload.php';
require_once '../../src/Utils/is_uppercased.php';
require_once '../../src/scan-controllers.php';
require_once '../../src/Internal/AssocArrayObjectSyntax.php';
require_once '../../src/Controller.php';
require_once '../../src/Nyxt.php';
// imports above are not required in your case
$framework = new \Nyxt\Base;
$framework->run();
