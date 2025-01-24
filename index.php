<?php
require_once('./vendor/autoload.php');

use App\Core\Routes;

$routes = new Routes();
$routes->run();
