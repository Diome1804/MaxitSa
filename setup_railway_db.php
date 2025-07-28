<?php

echo "🚂 Configuration Base de Données Railway\n";
echo "========================================\n\n";

// Charger la configuration
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/env.php';

echo "📊 Configuration détectée :\n";
echo "DSN: " . dsn . "\n";
echo "User: " . DB_USER . "\n";
echo "Password: " . (DB_PASSWORD ? '***DÉFINI***' : 'NON DÉFINI') . "\n\n";

try {
    echo "🔌 Test de connexion...\n";
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie !\n\n";

    // Exécuter les migrations
    echo "🗃 Exécution des migrations...\n";
    
    $migrations = [
        'migrations/migration.php',
        'migrations/migration_woyofal.php',
        'migrations/migration_depot.php', 
        'migrations/migration_enum_types.php'
    ];
    
    foreach ($migrations as $migration) {
        if (file_exists(__DIR__ . '/' . $migration)) {
            echo "  - Exécution de {$migration}...\n";
            
            // Définir les constantes nécessaires
            if (!defined('DB_USER')) define('DB_USER', DB_USER);
            if (!defined('DB_PASSWORD')) define('DB_PASSWORD', DB_PASSWORD);
            if (!defined('dsn')) define('dsn', dsn);
            
            include __DIR__ . '/' . $migration;
            echo "    ✅ Terminée\n";
        } else {
            echo "    ⚠️  {$migration} non trouvée\n";
        }
    }
    
    // Exécuter le seeder
    echo "\n🌱 Exécution du seeder...\n";
    if (file_exists(__DIR__ . '/seeders/seeder.php')) {
        include __DIR__ . '/seeders/seeder.php';
        echo "✅ Seeder terminé\n";
    } else {
        echo "⚠️  Seeder non trouvé\n";
    }
    
    echo "\n🎉 Configuration Railway terminée avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
