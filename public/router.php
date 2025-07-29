<?php
// Router pour le serveur PHP intégré
// Ce fichier gère le routage quand on utilise php -S

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

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Fichiers autorisés à être servis directement
$allowedFiles = [
    '/debug_render.php',
    '/debug_appdaf.php', 
    '/test_cni_endpoint.php',
    '/fix_https.php',
    '/simple_test.php'
];

// Si c'est un fichier autorisé et qu'il existe, le servir directement
if (in_array($requestPath, $allowedFiles) && file_exists(__DIR__ . $requestPath)) {
    return false; // Laisser le serveur intégré servir le fichier
}

// Si c'est un autre fichier statique existant (CSS, JS, images), le servir directement
if ($requestPath !== '/' && file_exists(__DIR__ . $requestPath) && substr($requestPath, -4) !== '.php') {
    return false; // Laisser le serveur intégré servir le fichier
}

// Sinon, rediriger vers index.php pour le routage
$_SERVER['REQUEST_URI'] = $requestUri;
require_once __DIR__ . '/index.php';
