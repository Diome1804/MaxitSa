<?php
echo "🔍 Debug Variables d'Environnement\n";
echo "==================================\n\n";

echo "📊 Variables d'environnement détectées :\n";
echo "RENDER: " . (getenv('RENDER') ? 'OUI' : 'NON') . "\n";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'NON DÉFINI') . "\n";
echo "DB_PORT: " . (getenv('DB_PORT') ?: 'NON DÉFINI') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'NON DÉFINI') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ?: 'NON DÉFINI') . "\n";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '***DÉFINI***' : 'NON DÉFINI') . "\n";
echo "APP_URL: " . (getenv('APP_URL') ?: 'NON DÉFINI') . "\n";

echo "\n🔧 Test de chargement de la configuration...\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/app/config/env.php';
    
    echo "✅ Configuration chargée !\n";
    echo "DSN: " . dsn . "\n";
    echo "DB_USER: " . DB_USER . "\n";
    echo "DB_PASSWORD: " . (DB_PASSWORD ? '***DÉFINI***' : 'NON DÉFINI') . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🔌 Test de connexion...\n";

try {
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Connexion réussie !\n";
} catch (Exception $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
}
