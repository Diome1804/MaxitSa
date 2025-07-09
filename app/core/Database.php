<?php

namespace App\Maxit\Core;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            // Charger le fichier .env
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2)); // Va à la racine
            $dotenv->load();

            $driver = $_ENV['DB_DRIVER'] ?? 'pgsql';
            $host   = $_ENV['DB_HOST'] ?? 'localhost';
            $port   = $_ENV['DB_PORT'] ?? '5432';
            $dbname = $_ENV['DB_NAME'] ?? '';
            $user   = $_ENV['DB_USER'] ?? '';
            $pass   = $_ENV['DB_PASS'] ?? '';

            $dsn = "$driver:host=$host;port=$port;dbname=$dbname";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
