<?php

// Script de déploiement pour Render
echo "🚀 Début du déploiement MAXITSA...\n";

// Charger les variables d'environnement
require_once __DIR__ . '/vendor/autoload.php';

// Construire le DSN depuis les variables d'environnement Render
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: '5432';
$dbName = getenv('DB_NAME') ?: 'maxitsa';
$dbUser = getenv('DB_USER') ?: 'postgres';
$dbPassword = getenv('DB_PASSWORD') ?: '';

$dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";

echo "📊 Connexion à la base de données...\n";
echo "Host: {$dbHost}:{$dbPort}\n";
echo "Database: {$dbName}\n";
echo "User: {$dbUser}\n";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "✅ Connexion réussie!\n";
    
    // Exécuter les migrations dans l'ordre
    $migrations = [
        'migrations/migration.php',
        'migrations/migration_woyofal.php', 
        'migrations/migration_depot.php',
        'migrations/migration_enum_types.php'
    ];
    
    foreach ($migrations as $migration) {
        if (file_exists(__DIR__ . '/' . $migration)) {
            echo "🔄 Exécution de {$migration}...\n";
            
            // Définir les constantes nécessaires
            define('DB_USER', $dbUser);
            define('DB_PASSWORD', $dbPassword);
            define('dsn', $dsn);
            
            // Inclure et exécuter la migration
            try {
                include __DIR__ . '/' . $migration;
                echo "✅ {$migration} terminée\n";
            } catch (Exception $e) {
                echo "⚠️  Erreur dans {$migration}: " . $e->getMessage() . "\n";
                // Continuer avec les autres migrations
            }
        } else {
            echo "⚠️  Migration {$migration} non trouvée\n";
        }
    }
    
    echo "🎉 Déploiement terminé avec succès!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur de déploiement: " . $e->getMessage() . "\n";
    exit(1);
}
