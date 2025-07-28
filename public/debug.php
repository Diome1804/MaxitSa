<?php
header('Content-Type: text/plain; charset=utf-8');

echo "🔍 MAXITSA - Debug Variables d'Environnement\n";
echo "=============================================\n\n";

echo "📊 Variables d'environnement système :\n";
echo "RENDER: " . (getenv('RENDER') ? getenv('RENDER') : 'NON DÉFINI') . "\n";
echo "DB_HOST: " . (getenv('DB_HOST') ? getenv('DB_HOST') : 'NON DÉFINI') . "\n";
echo "DB_PORT: " . (getenv('DB_PORT') ? getenv('DB_PORT') : 'NON DÉFINI') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ? getenv('DB_NAME') : 'NON DÉFINI') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ? getenv('DB_USER') : 'NON DÉFINI') . "\n";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '***MASQUÉ*** (longueur: ' . strlen(getenv('DB_PASSWORD')) . ')' : 'NON DÉFINI') . "\n";
echo "APP_URL: " . (getenv('APP_URL') ? getenv('APP_URL') : 'NON DÉFINI') . "\n";
echo "PORT: " . (getenv('PORT') ? getenv('PORT') : 'NON DÉFINI') . "\n";

echo "\n📋 Variables \$_ENV :\n";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NON DÉFINI') . "\n";
echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'NON DÉFINI') . "\n";

echo "\n🔧 Test de chargement de la configuration...\n";

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../app/config/env.php';
    
    echo "✅ Configuration chargée !\n";
    echo "DSN final: " . dsn . "\n";
    echo "DB_USER final: " . DB_USER . "\n";
    echo "DB_PASSWORD final: " . (DB_PASSWORD ? '***MASQUÉ*** (longueur: ' . strlen(DB_PASSWORD) . ')' : 'NON DÉFINI') . "\n";
    
    echo "\n🔌 Test de connexion à la base de données...\n";
    
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Connexion à la base de données réussie !\n";
    
    // Test d'une requête simple
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "📋 Version PostgreSQL: " . $version . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🚀 Debug terminé.\n";
