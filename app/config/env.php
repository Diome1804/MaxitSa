<?php

use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


//ici on defini les constantes qu on va utiliser dans notre application
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('APP_URL', $_ENV['APP_URL']);
define('dsn', $_ENV['dsn'] );

// URLs des services externes
define('APPDAF_API_URL', $_ENV['APPDAF_API_URL']);
define('WOYOFAL_API_URL', $_ENV['WOYOFAL_API_URL']);
