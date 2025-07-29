<?php

namespace App\Core;

use App\Core\Middlewares\Auth;
use App\Core\DependencyContainer;
//use App\Core\Middlewares\Guest;

class Router
{
    private static ?Router $instance = null;

    public static function getInstance(): Router
    {
        if (self::$instance === null) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    public static function resolve($uris)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Forcer HTTPS sur les environnements de production
        if (!self::isHttps() && (getenv('RENDER') === 'true' || getenv('PRODUCTION') === 'true')) {
            $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: $redirectURL", true, 301);
            exit();
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        // Extraire seulement le path sans les paramètres GET
        $parsedUri = parse_url($requestUri);
        $currentUri = rtrim($parsedUri['path'] ?? '/', '/') ?: '/';

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

            // Utiliser le conteneur d'injection de dépendances
            $container = DependencyContainer::getInstance();
            $controllerServiceId = self::getControllerServiceId($controllerClass);

            if ($controllerServiceId) {
                $controller = $container->get($controllerServiceId);
            } else {
                // Fallback pour les contrôleurs non configurés
                $controller = new $controllerClass();
            }

            $controller->$method();
        } else {
            http_response_code(404);
            echo "<h1>404 - Page Not Found</h1>";
        }
    }

    private static function getControllerServiceId(string $controllerClass): ?string
    {
        $mapping = [
            'Src\\Controller\\SecurityController' => 'securityController',
            'Src\\Controller\\CompteController' => 'compteController',
            'Src\\Controller\\WoyofalController' => 'woyofalController',
            'Src\\Controller\\DepotController' => 'depotController',
        ];

        return $mapping[$controllerClass] ?? null;
    }

    private static function isHttps(): bool
    {
        // Sur Render, toujours considérer comme HTTPS
        if (getenv('RENDER') === 'true') {
            return true;
        }
        
        // Vérifier HTTPS de plusieurs manières pour autres environnements
        return (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
            (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        );
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
