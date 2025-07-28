<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../app/config/bootstrap.php";
require_once __DIR__ . '/../routes/route.web.php';

use App\Core\Router;

Router::resolve($routes);
