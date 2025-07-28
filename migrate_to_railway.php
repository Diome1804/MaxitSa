<?php

echo "🚂 Migration vers Railway PostgreSQL\n";
echo "====================================\n\n";

// Configuration directe Railway
$railwayUrl = 'postgresql://postgres:IoiQfHDMYkFAXvwkHaawDOlpkKJPLslx@shuttle.proxy.rlwy.net:30832/railway';
$urlParts = parse_url($railwayUrl);

$dbHost = $urlParts['host'];
$dbPort = $urlParts['port'];
$dbName = ltrim($urlParts['path'], '/');
$dbUser = $urlParts['user'];
$dbPassword = $urlParts['pass'];

$dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";

echo "📊 Configuration Railway :\n";
echo "Host: {$dbHost}:{$dbPort}\n";
echo "Database: {$dbName}\n";
echo "User: {$dbUser}\n\n";

try {
    echo "🔌 Connexion à Railway...\n";
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie !\n\n";

    // Définir les constantes pour les migrations
    define('DB_USER', $dbUser);
    define('DB_PASSWORD', $dbPassword);
    define('dsn', $dsn);

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
            
            try {
                include __DIR__ . '/' . $migration;
                echo "    ✅ Terminée\n";
            } catch (Exception $e) {
                echo "    ❌ Erreur: " . $e->getMessage() . "\n";
            }
        } else {
            echo "    ⚠️  {$migration} non trouvée\n";
        }
    }
    
    echo "\n🌱 Exécution du seeder...\n";
    if (file_exists(__DIR__ . '/seeders/seeder.php')) {
        try {
            // Charger le seeder qui utilisera les mêmes constantes
            require_once __DIR__ . '/vendor/autoload.php';
            
            // Le seeder utilisera notre configuration
            $_ENV['DATABASE_URL'] = $railwayUrl;
            
            include __DIR__ . '/seeders/seeder.php';
            echo "✅ Seeder terminé\n";
        } catch (Exception $e) {
            echo "❌ Erreur seeder: " . $e->getMessage() . "\n";
        }
    } else {
        echo "⚠️  Seeder non trouvé\n";
    }
    
    // Vérifier les données créées
    echo "\n📊 Vérification des données...\n";
    $stmt = $pdo->query('SELECT COUNT(*) FROM "user"');
    $userCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM compte');
    $compteCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM transactions');
    $transactionCount = $stmt->fetchColumn();
    
    echo "  - Utilisateurs: {$userCount}\n";
    echo "  - Comptes: {$compteCount}\n"; 
    echo "  - Transactions: {$transactionCount}\n";
    
    echo "\n🎉 Migration Railway terminée avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
