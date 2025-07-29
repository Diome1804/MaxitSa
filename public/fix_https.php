<?php
/**
 * Script pour forcer HTTPS et corriger APP_URL sur Render
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>Fix HTTPS</title></head><body><pre style='font-family: monospace; background: #f4f4f4; padding: 20px;'>";

echo "=== FIX HTTPS MAXITSA ===\n\n";

echo "DIAGNOSTIC ACTUEL :\n";
echo "- APP_URL définie : " . APP_URL . "\n";
echo "- HOST actuel : " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "\n";
echo "- HTTPS détecté : " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'OUI' : 'NON') . "\n";
echo "- X-Forwarded-Proto : " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'N/A') . "\n";
echo "- RENDER env : " . (getenv('RENDER') ?: 'Non définie') . "\n\n";

// Forcer la redefinition d'APP_URL si on est sur Render
if (getenv('RENDER') === 'true' && isset($_SERVER['HTTP_HOST'])) {
    $correctAppUrl = 'https://' . $_SERVER['HTTP_HOST'];
    echo "CORRECTION APPLIQUÉE :\n";
    echo "- APP_URL corrigée : $correctAppUrl\n";
    
    // Redéfinir la constante (nécessite un petit hack)
    if (function_exists('runkit_constant_redefine')) {
        runkit_constant_redefine('APP_URL', $correctAppUrl);
        echo "- Constante redéfinie avec runkit\n";
    } else {
        echo "- Impossibilité de redéfinir la constante directement\n";
        echo "- Utiliser \$correctAppUrl = '$correctAppUrl' dans les templates\n";
    }
}

// Tester la génération d'URLs
echo "\nTEST GÉNÉRATION D'URLS :\n";
$testAppUrl = (getenv('RENDER') === 'true' && isset($_SERVER['HTTP_HOST'])) 
    ? 'https://' . $_SERVER['HTTP_HOST'] 
    : APP_URL;

echo "- Login URL : " . $testAppUrl . "/login\n";
echo "- Register URL : " . $testAppUrl . "/register\n";
echo "- API CNI URL : " . $testAppUrl . "/api/verifier-cni\n";

echo "\nSOLUTION RECOMMANDÉE :\n";
echo "1. Créer un helper getAppUrl() qui force HTTPS sur Render\n";
echo "2. Remplacer APP_URL par ce helper dans tous les templates\n";
echo "3. Nettoyer le cache navigateur (Ctrl+Shift+R)\n";

echo "</pre></body></html>";
