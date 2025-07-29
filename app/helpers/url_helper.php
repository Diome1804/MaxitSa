<?php
/**
 * Helper pour les URLs - Force HTTPS sur Render
 */

if (!function_exists('getAppUrl')) {
    /**
     * Retourne l'URL de base de l'application en forçant HTTPS sur Render
     */
    function getAppUrl(): string {
        // Sur Render, toujours utiliser HTTPS
        if (getenv('RENDER') === 'true' && isset($_SERVER['HTTP_HOST'])) {
            return 'https://' . $_SERVER['HTTP_HOST'];
        }
        
        // Sinon utiliser APP_URL définie
        return defined('APP_URL') ? APP_URL : 'http://localhost:8000';
    }
}

if (!function_exists('url')) {
    /**
     * Génère une URL complète vers une route
     */
    function url(string $path = ''): string {
        $baseUrl = getAppUrl();
        $path = ltrim($path, '/');
        return $path ? $baseUrl . '/' . $path : $baseUrl;
    }
}

if (!function_exists('forceHttps')) {
    /**
     * Force la redirection HTTPS si nécessaire
     */
    function forceHttps(): void {
        if (getenv('RENDER') === 'true') {
            // Sur Render, vérifier si on est en HTTP
            $isHttps = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                      (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            
            if (!$isHttps && isset($_SERVER['HTTP_HOST'])) {
                $httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . ($_SERVER['REQUEST_URI'] ?? '');
                header("Location: $httpsUrl", true, 301);
                exit();
            }
        }
    }
}
