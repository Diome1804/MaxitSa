<?php

namespace App\Core;

class Router
{
    private static array $routes = [];

    public static function get(string $uri, string $controller, string $action): void
    {
        self::$routes['GET'][$uri] = ['controller' => $controller, 'action' => $action];
    }

    public static function post(string $uri, string $controller, string $action): void
    {
        self::$routes['POST'][$uri] = ['controller' => $controller, 'action' => $action];
    }

    public static function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Debug
        error_log("Router - Méthode: {$method}, URI: {$uri}");

        // Nettoyer l'URI
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        // Redirection vers /login si on accède à la racine
        if ($uri === '/') {
            header('Location: /login');
            exit;
        }

        // Vérifier si la route existe
        if (isset(self::$routes[$method][$uri])) {
            $route = self::$routes[$method][$uri];
            $controllerName = $route['controller'];
            $action = $route['action'];

            error_log("Route trouvée - Contrôleur: {$controllerName}, Action: {$action}");

            // Vérifier que la classe du contrôleur existe
            if (!class_exists($controllerName)) {
                error_log("Contrôleur introuvable: {$controllerName}");
                self::render404("Contrôleur introuvable: {$controllerName}");
                return;
            }

            try {
                $controller = new $controllerName();

                // Vérifier que la méthode existe
                if (!method_exists($controller, $action)) {
                    error_log("Action introuvable: {$action}");
                    self::render404("Action introuvable: {$action}");
                    return;
                }

                // Exécuter l'action du contrôleur
                $controller->$action();
            } catch (Exception $e) {
                error_log("Erreur lors de l'exécution: " . $e->getMessage());
                self::render404("Erreur: " . $e->getMessage());
            }
        } else {
            error_log("Route non trouvée pour {$method} {$uri}");
            self::render404("Route non trouvée: {$method} {$uri}");
        }
    }

    /**
     * Rendre la page 404
     */
    private static function render404(string $message = "Page introuvable"): void
    {
        http_response_code(404);
        
        echo "<div style='padding: 20px; font-family: Arial, sans-serif;'>";
        echo "<h1>404 - {$message}</h1>";
        echo "<p><strong>URI demandée:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</p>";
        echo "<p><strong>Méthode:</strong> " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "</p>";
        echo "<p><strong>Routes disponibles:</strong></p>";
        echo "<pre>" . print_r(self::$routes, true) . "</pre>";
        echo "<a href='/login'>Retour à la connexion</a>";
        echo "</div>";
    }
}
