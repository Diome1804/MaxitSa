<?php

// Forcer HTTPS sur Render
$isRender = (
    strpos($_SERVER['HTTP_HOST'], '.onrender.com') !== false ||
    isset($_ENV['RENDER']) ||
    isset($_SERVER['RENDER'])
);

if ($isRender) {
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
        $_SERVER['SERVER_PORT'] == 443
    );
    
    if (!$isHttps) {
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
