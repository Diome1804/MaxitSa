<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configuration directe pour Railway
$databaseUrl = 'postgresql://postgres:IoiQfHDMYkFAXvwkHaawDOlpkKJPLslx@shuttle.proxy.rlwy.net:30832/railway';
$urlParts = parse_url($databaseUrl);
define('DB_USER', $urlParts['user']);
define('DB_PASSWORD', $urlParts['pass']);
define('dsn', "pgsql:host={$urlParts['host']};dbname=" . ltrim($urlParts['path'], '/') . ";port={$urlParts['port']}");

use App\Core\DependencyContainer;
use Src\Service\SecurityService;

echo "=== TEST DE L'APPLICATION CORRIGÉE ===\n";

try {
    // Initialiser le container
    $container = DependencyContainer::getInstance();
    $securityService = $container->get(SecurityService::class);
    
    // Test de connexion
    $testData = [
        'numero' => '778232295',
        'password' => 'passer123'
    ];
    
    echo "Test de connexion avec :\n";
    echo "- Téléphone: {$testData['numero']}\n";
    echo "- Mot de passe: {$testData['password']}\n\n";
    
    $user = $securityService->authenticate($testData);
    
    if ($user) {
        echo "✅ CONNEXION RÉUSSIE !\n";
        echo "- Utilisateur: {$user->getNom()} {$user->getPrenom()}\n";
        echo "- ID: {$user->getId()}\n";
        echo "- Téléphone: {$user->getTelephone()}\n";
    } else {
        echo "❌ CONNEXION ÉCHOUÉE\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
