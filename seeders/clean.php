<?php

// Nettoyer les tables avant le seeding
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

try {
    $pdo = new PDO(
        dsn,
        DB_USER, 
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "Nettoyage des tables...\n";
    
    // Vider les tables dans le bon ordre (à cause des clés étrangères)
    $pdo->exec('TRUNCATE transactions, compte, "user", type_user RESTART IDENTITY CASCADE');
    
    echo "✅ Tables vidées avec succès !\n";
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
