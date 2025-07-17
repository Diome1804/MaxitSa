<?php

namespace App\Core;

use App\Core\Middlewares\Auth;
//use App\Core\Middlewares\Guest;

class Router
{
    private static ?Router $instance = null;

    public static function getInstance(): Router
    {
        if(self::$instance === null){
            self::$instance = new Router();
        }
        return self::$instance;
    }

    public static function resolve($uris)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        // ✅ CORRIGÉ : trim avec '/' au lieu de ''
        $currentUri = rtrim($requestUri, '/') ?: '/';
        
        error_log("=== ROUTER ===");
        error_log("URI: '$currentUri'");

        if (isset($uris[$currentUri])) {
            $route = $uris[$currentUri];
            $controllerClass = $route['controller'];
            $method = $route['method'];
            $middlewares = $route['middlewares'] ?? [];

            // ✅ ACTIVEZ LES MIDDLEWARES
            self::runMiddlewares($middlewares);

            // Charger le contrôleur
            if (!class_exists($controllerClass)) {
                $classFile = str_replace(['Src\\Controller\\', '\\'], ['../src/controller/', '/'], $controllerClass) . '.php';
                if (file_exists($classFile)) {
                    require_once $classFile;
                }
            }

            $controller = new $controllerClass();
            $controller->$method();
        } else {
            http_response_code(404);
            echo "<h1>404 - Page Not Found</h1>";
        }
    }

    private static function runMiddlewares(array $middlewares): void
{
    foreach ($middlewares as $middleware) {
        switch ($middleware) {
            case 'auth':
                // ✅ MIDDLEWARE SIMPLE SANS CLASSE
                if (!isset($_SESSION['user'])) {
                    error_log("❌ Auth: Utilisateur non connecté");
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    header('Location: /');
                    exit();
                }
                error_log("✅ Auth: Utilisateur connecté");
                break;
            
            default:
                error_log("Middleware inconnu: $middleware");
                break;
        }
    }
}

}
