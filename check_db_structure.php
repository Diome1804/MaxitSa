<?php

// Configuration directe pour Railway
$databaseUrl = 'postgresql://postgres:IoiQfHDMYkFAXvwkHaawDOlpkKJPLslx@shuttle.proxy.rlwy.net:30832/railway';
$urlParts = parse_url($databaseUrl);
$dbHost = $urlParts['host'];
$dbPort = $urlParts['port'] ?? '5432';
$dbName = ltrim($urlParts['path'], '/');
$dbUser = $urlParts['user'];
$dbPassword = $urlParts['pass'];

$dsn = "pgsql:host={$dbHost};dbname={$dbName};port={$dbPort}";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à Railway\n\n";
    
    // Vérifier les tables existantes
    $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables existantes :\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Vérifier la structure de type_user
    if (in_array('type_user', $tables)) {
        echo "\nStructure de la table type_user :\n";
        $columns = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'type_user'")->fetchAll();
        foreach ($columns as $col) {
            echo "- {$col['column_name']} ({$col['data_type']})\n";
        }
    }
    
    // Vérifier la structure de user
    if (in_array('user', $tables)) {
        echo "\nStructure de la table user :\n";
        $columns = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'user'")->fetchAll();
        foreach ($columns as $col) {
            echo "- {$col['column_name']} ({$col['data_type']})\n";
        }
    }
    
    // Vérifier la structure de compte
    if (in_array('compte', $tables)) {
        echo "\nStructure de la table compte :\n";
        $columns = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'compte'")->fetchAll();
        foreach ($columns as $col) {
            echo "- {$col['column_name']} ({$col['data_type']})\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
