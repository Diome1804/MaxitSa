<?php

echo "🚂 Mise à jour données Railway avec numéros sénégalais\n";
echo "====================================================\n\n";

// Configuration directe Railway
$railwayUrl = 'postgresql://postgres:IoiQfHDMYkFAXvwkHaawDOlpkKJPLslx@shuttle.proxy.rlwy.net:30832/railway';
$urlParts = parse_url($railwayUrl);
$dsn = "pgsql:host={$urlParts['host']};port={$urlParts['port']};dbname=" . ltrim($urlParts['path'], '/');

try {
    $pdo = new PDO($dsn, $urlParts['user'], $urlParts['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion Railway réussie !\n\n";
    
    // Vider les données existantes
    echo "🧹 Nettoyage des données existantes...\n";
    $pdo->exec("TRUNCATE transactions, compte, \"user\", type_user RESTART IDENTITY CASCADE");
    echo "✅ Données supprimées\n\n";
    
    // Définir les constantes pour le seeder
    define('DB_USER', $urlParts['user']);
    define('DB_PASSWORD', $urlParts['pass']);
    define('dsn', $dsn);
    
    // Charger et exécuter le seeder modifié
    echo "🌱 Insertion des nouvelles données...\n";
    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/seeders/seeder.php';
    
    echo "\n🎉 Mise à jour terminée avec succès !\n\n";
    
    // Vérification des données
    $stmt = $pdo->query('SELECT nom, prenom, telephone FROM "user"');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "👥 Utilisateurs créés :\n";
    foreach ($users as $user) {
        echo "  - {$user['nom']} {$user['prenom']} ({$user['telephone']})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
