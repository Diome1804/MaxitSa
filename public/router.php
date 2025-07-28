<?php
// Router pour le serveur PHP intégré
// Ce fichier gère le routage quand on utilise php -S

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Si c'est un fichier statique existant, le servir directement
if ($requestPath !== '/' && file_exists(__DIR__ . $requestPath)) {
    return false; // Laisser le serveur intégré servir le fichier
}

// Sinon, rediriger vers index.php pour le routage
$_SERVER['REQUEST_URI'] = $requestUri;
require_once __DIR__ . '/index.php';
