<?php

namespace App\Core;

class MiddlewareLoader
{
    private static ?MiddlewareLoader $instance = null;
    private static array $middlewares = [];

    public static function getInstance(): MiddlewareLoader
    {
        if (self::$instance === null) {
            self::$instance = new MiddlewareLoader();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->loadMiddlewares();
    }

    /**
     * Charger les middlewares depuis le fichier de configuration
     */
    private function loadMiddlewares(): void
    {
        $middlewaresFile = __DIR__ . '/../config/middlewares.php';
        
        if (file_exists($middlewaresFile)) {
            self::$middlewares = require $middlewaresFile;
        }
    }

    /**
     * Exécuter un middleware
     */
    public static function execute(string $middlewareName, ...$params)
    {
        self::getInstance();
        
        if (!isset(self::$middlewares[$middlewareName])) {
            throw new \Exception("Middleware '{$middlewareName}' not found");
        }
        
        $middlewareClass = self::$middlewares[$middlewareName];
        $middleware = new $middlewareClass();
        
        return $middleware(...$params);
    }

    /**
     * Vérifier si un middleware existe
     */
    public static function has(string $middlewareName): bool
    {
        self::getInstance();
        return isset(self::$middlewares[$middlewareName]);
    }

    /**
     * Obtenir tous les middlewares
     */
    public static function getAll(): array
    {
        self::getInstance();
        return self::$middlewares;
    }
}
