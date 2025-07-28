<?php

use Dotenv\Dotenv;

// Priorité à l'URL de base de données (Railway DATABASE_URL ou variables séparées)
$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {
    // Utiliser l'URL complète (Railway ou autres services)
    $urlParts = parse_url($databaseUrl);
    $dbHost = $urlParts['host'];
    $dbPort = $urlParts['port'] ?? '5432';
    $dbName = ltrim($urlParts['path'], '/');
    $dbUser = $urlParts['user'];
    $dbPassword = $urlParts['pass'];
} else {
    // Variables d'environnement séparées ou locales
    $dbHost = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? 'localhost';
    $dbPort = getenv('DB_PORT') ?: $_ENV['DB_PORT'] ?? '5432';
    $dbName = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? 'maxitsa';
    $dbUser = getenv('DB_USER') ?: $_ENV['DB_USER'] ?? 'postgres';
    $dbPassword = getenv('DB_PASSWORD') ?: $_ENV['DB_PASSWORD'] ?? '';
}

// Si on n'est pas sur Render, charger le fichier .env local
if (!getenv('RENDER') && !getenv('DATABASE_URL')) {
    $envFile = '.env.local';
    if (file_exists(__DIR__ . '/../../' . $envFile)) {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../', $envFile);
        $dotenv->load();
        
        // Recharger les variables après le .env.local
        $databaseUrl = getenv('DATABASE_URL') ?: $_ENV['DATABASE_URL'] ?? null;
        
        if ($databaseUrl) {
            $urlParts = parse_url($databaseUrl);
            $dbHost = $urlParts['host'];
            $dbPort = $urlParts['port'] ?? '5432';
            $dbName = ltrim($urlParts['path'], '/');
            $dbUser = $urlParts['user'];
            $dbPassword = $urlParts['pass'];
        } else {
            $dbHost = $_ENV['db_host'] ?? 'localhost';
            $dbPort = $_ENV['db_port'] ?? '5432';
            $dbName = $_ENV['db_name'] ?? 'maxitsa';
            $dbUser = $_ENV['db_user'] ?? 'postgres';
            $dbPassword = $_ENV['db_password'] ?? '';
        }
    }
}

// Construction du DSN
$dsn = "pgsql:host={$dbHost};dbname={$dbName};port={$dbPort}";

//ici on defini les constantes qu on va utiliser dans notre application
define('DB_USER', $dbUser);
define('DB_PASSWORD', $dbPassword);
define('APP_URL', getenv('APP_URL') ?: $_ENV['APP_URL'] ?? 'https://maxitsa-app.onrender.com');
define('dsn', $dsn);

// URLs des services externes
define('APPDAF_API_URL', getenv('APPDAF_API_URL') ?: $_ENV['APPDAF_API_URL'] ?? 'https://appdaff-zwqf.onrender.com');
define('WOYOFAL_API_URL', getenv('WOYOFAL_API_URL') ?: $_ENV['WOYOFAL_API_URL'] ?? 'https://appwoyofal.onrender.com');
