<?php
/**
 * Script de debug pour diagnostiquer les problèmes sur Render
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

// En-tête HTML pour affichage web
if (!isset($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'curl') === false) {
    header('Content-Type: text/html; charset=utf-8');
    echo "<!DOCTYPE html><html><head><title>Debug Render</title></head><body><pre style='font-family: monospace; background: #f4f4f4; padding: 20px; border-radius: 8px;'>";
}

echo "=== DEBUG RENDER ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

echo "=== VARIABLES D'ENVIRONNEMENT ===\n";
echo "RENDER: " . (getenv('RENDER') ?: 'Non définie') . "\n";
echo "APP_URL (getenv): " . (getenv('APP_URL') ?: 'Non définie') . "\n";
echo "APP_URL (constante): " . (defined('APP_URL') ? APP_URL : 'Non définie') . "\n";
echo "DATABASE_URL: " . (getenv('DATABASE_URL') ? 'Définie (masquée)' : 'Non définie') . "\n\n";

echo "=== INFORMATIONS SERVEUR ===\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Non définie') . "\n";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'Non définie') . "\n";
echo "HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'Non définie') . "\n";
echo "HTTP_X_FORWARDED_SSL: " . ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? 'Non définie') . "\n";
echo "SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'Non définie') . "\n";
echo "REQUEST_SCHEME: " . ($_SERVER['REQUEST_SCHEME'] ?? 'Non définie') . "\n\n";

echo "=== DÉTECTION HTTPS ===\n";
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
);
echo "HTTPS détecté: " . ($isHttps ? '✅ OUI' : '❌ NON') . "\n\n";

echo "=== URL GÉNÉRÉES ===\n";
echo "APP_URL: " . APP_URL . "\n";
echo "URL actuelle reconstruite: ";
$protocol = $isHttps ? 'https' : 'http';
$currentUrl = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'inconnu');
echo "$currentUrl\n\n";

echo "=== TEST GÉNÉRATION D'URLs ===\n";
echo "Login URL: " . APP_URL . "/login\n";
echo "Register URL: " . APP_URL . "/register\n";
echo "Depot URL: " . APP_URL . "/depot/effectuer\n";
echo "API CNI URL: " . APP_URL . "/api/verifier-cni\n\n";

echo "=== RECOMMANDATIONS ===\n";
if (!$isHttps) {
    echo "❌ HTTPS non détecté - problème de configuration proxy\n";
}
if (strpos(APP_URL, 'http://') === 0) {
    echo "❌ APP_URL utilise HTTP au lieu de HTTPS\n";
    echo "   Valeur actuelle: " . APP_URL . "\n";
    echo "   Devrait être: https://" . ($_SERVER['HTTP_HOST'] ?? 'maxitsa-app.onrender.com') . "\n";
}
if (strpos(APP_URL, 'https://') === 0) {
    echo "✅ APP_URL utilise HTTPS correctement\n";
}

echo "\n=== VÉRIFICATIONS RENDER.YAML ===\n";
if (file_exists(__DIR__ . '/../render.yaml')) {
    $renderConfig = file_get_contents(__DIR__ . '/../render.yaml');
    if (strpos($renderConfig, 'APP_URL') !== false) {
        echo "✅ APP_URL définie dans render.yaml\n";
        // Extraire la valeur
        if (preg_match('/APP_URL[^:]*:\s*(.*)/', $renderConfig, $matches)) {
            echo "   Valeur: " . trim($matches[1]) . "\n";
        }
    } else {
        echo "❌ APP_URL non trouvée dans render.yaml\n";
    }
}

echo "\n=== FIN DEBUG ===\n";

// Fermer les balises HTML
if (!isset($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'curl') === false) {
    echo "</pre></body></html>";
}
