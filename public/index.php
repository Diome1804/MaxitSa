<?php

// Forcer HTTPS sur Render
if (isset($_ENV['RENDER']) && $_ENV['RENDER'] === 'true') {
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
    );
    
    if (!$isHttps && strpos($_SERVER['HTTP_HOST'], '.onrender.com') !== false) {
        $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $redirectURL", true, 301);
        exit();
    }
}

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../app/config/bootstrap.php";
require_once __DIR__ . '/../routes/route.web.php';

use App\Core\Router;

Router::resolve($routes);
