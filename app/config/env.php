<?php

use Dotenv\Dotenv;

// Charger le fichier .env approprié selon l'environnement
$envFile = file_exists(__DIR__ . '/../../.env.production') ? '.env.production' : '.env';
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../', $envFile);
$dotenv->load();

// Priorité aux variables d'environnement système (Render) puis fichier .env
$dbHost = $_ENV['DB_HOST'] ?? $_ENV['db_host'] ?? getenv('DB_HOST') ?: 'localhost';
$dbPort = $_ENV['DB_PORT'] ?? $_ENV['db_port'] ?? getenv('DB_PORT') ?: '5432';
$dbName = $_ENV['DB_NAME'] ?? $_ENV['db_name'] ?? getenv('DB_NAME') ?: 'maxitsa';
$dbUser = $_ENV['DB_USER'] ?? $_ENV['db_user'] ?? getenv('DB_USER') ?: 'postgres';
$dbPassword = $_ENV['DB_PASSWORD'] ?? $_ENV['db_password'] ?? getenv('DB_PASSWORD') ?: '';

// Construction du DSN
if ($dbHost !== 'localhost' || getenv('RENDER')) {
    // Environnement de production (Render)
    $dsn = "pgsql:host={$dbHost};dbname={$dbName};port={$dbPort}";
} else {
    // Environnement local
    $dsn = $_ENV['dsn'] ?? "pgsql:host=localhost;dbname=maxitsa;port=5432";
}

//ici on defini les constantes qu on va utiliser dans notre application
define('DB_USER', $dbUser);
define('DB_PASSWORD', $dbPassword);
define('APP_URL', $_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://localhost:8000');
define('dsn', $dsn);

// URLs des services externes
define('APPDAF_API_URL', $_ENV['APPDAF_API_URL'] ?? getenv('APPDAF_API_URL') ?: 'https://appdaff-zwqf.onrender.com');
define('WOYOFAL_API_URL', $_ENV['WOYOFAL_API_URL'] ?? getenv('WOYOFAL_API_URL') ?: 'https://appwoyofal.onrender.com');
