<?php
header('Content-Type: text/plain; charset=utf-8');

echo "🔍 Statut Base de Données MAXITSA\n";
echo "=================================\n\n";

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../app/config/env.php';
    
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion réussie !\n\n";
    
    // Lister toutes les tables
    echo "📋 Tables disponibles :\n";
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "❌ Aucune table trouvée - Les migrations n'ont pas encore été exécutées\n";
    } else {
        foreach ($tables as $table) {
            echo "  - {$table}\n";
        }
        
        echo "\n📊 Nombre d'enregistrements par table :\n";
        foreach ($tables as $table) {
            try {
                // Échapper le nom de table avec des guillemets si nécessaire
                $tableName = ($table === 'user') ? '"user"' : $table;
                $stmt = $pdo->query("SELECT COUNT(*) FROM {$tableName}");
                $count = $stmt->fetchColumn();
                echo "  - {$table}: {$count} enregistrements\n";
            } catch (Exception $e) {
                echo "  - {$table}: Erreur ({$e->getMessage()})\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n🚀 Status check terminé.\n";
