<?php
namespace App\core;


class Session 
{
    
    private static ?Session $instance = null;

    
    private function __construct() 
    {
    
    }

   
    private function __clone() {}

    
    public function __wakeup(): void
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    
    public static function getInstance(): Session
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    
    public function start(): void 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Définit une valeur dans la session
     */
    public function set(string $key, $value): void 
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une valeur de la session
     */
    public function get(string $key, $default = null) 
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Supprime une valeur de la session
     */
    public function unset(string $key): void 
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    /**
     * Détruit complètement la session
     */
    public function destroy(): void 
    {
        $this->start();
        
        // Vider le tableau de session
        $_SESSION = [];
        
        // // Supprimer le cookie de session si les cookies sont utilisés
        // if (ini_get("session.use_cookies")) {
        //     $params = session_get_cookie_params();
        //     setcookie(
        //         session_name(),
        //         '',
        //         time() - 42000,
        //         $params["path"],
        //         $params["domain"],
        //         $params["secure"],
        //         $params["httponly"]
        //     );
        // }
        
        // Détruire la session
        session_destroy();
    }

    /**
     * Vérifie si une clé existe dans la session
     */
    public function isset(string $key): bool 
    {
        $this->start();
        return isset($_SESSION[$key]);
    }

    /**
     * Alias pour isset (plus lisible)
     */
    public function has(string $key): bool 
    {
        return $this->isset($key);
    }

    /**
     * Vide complètement la session sans la détruire
     */
    public function clear(): void 
    {
        $this->start();
        $_SESSION = [];
    }

    /**
     * Retourne toutes les données de session
     */
    public function all(): array 
    {
        $this->start();
        return $_SESSION;
    }

    /**
     * Retourne l'ID de session actuel
     */
    public function getId(): string 
    {
        $this->start();
        return session_id();
    }

    /**
     * Régénère l'ID de session
     */
    public function regenerateId(bool $deleteOldSession = true): bool 
    {
        $this->start();
        return session_regenerate_id($deleteOldSession);
    }

    /**
     * Retourne le statut de la session
     */
    public function getStatus(): int 
    {
        return session_status();
    }

    /**
     * Vérifie si la session est active
     */
    public function isActive(): bool 
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
