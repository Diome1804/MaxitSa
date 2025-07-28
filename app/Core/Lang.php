<?php

namespace App\Core;

class Lang
{
    private static ?Lang $instance = null;
    private static string $currentLang = 'fr';
    private static array $translations = [];

    public static function getInstance(): Lang
    {
        if (self::$instance === null) {
            self::$instance = new Lang();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->loadTranslations();
    }

    /**
     * Définir la langue courante
     */
    public static function setLang(string $lang): void
    {
        if (in_array($lang, ['fr', 'en'])) {
            self::$currentLang = $lang;
            self::getInstance()->loadTranslations();
        }
    }

    /**
     * Obtenir la langue courante
     */
    public static function getCurrentLang(): string
    {
        return self::$currentLang;
    }

    /**
     * Charger les traductions pour la langue courante
     */
    private function loadTranslations(): void
    {
        $langFile = __DIR__ . '/../lang/' . self::$currentLang . '.php';
        
        if (file_exists($langFile)) {
            self::$translations = require $langFile;
        } else {
            // Fallback vers français si le fichier n'existe pas
            $fallbackFile = __DIR__ . '/../lang/fr.php';
            if (file_exists($fallbackFile)) {
                self::$translations = require $fallbackFile;
            }
        }
    }

    /**
     * Obtenir un message traduit
     * 
     * @param string $key Clé du message (ex: 'validation.required' ou 'account.create_success')
     * @param array $params Paramètres à remplacer dans le message
     * @return string
     */
    public static function get(string $key, array $params = []): string
    {
        self::getInstance(); // S'assurer que l'instance existe
        
        $keys = explode('.', $key);
        $value = self::$translations;
        
        // Naviguer dans le tableau de traductions
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key; // Retourner la clé si la traduction n'existe pas
            }
        }
        
        // Remplacer les paramètres dans le message
        if (!empty($params) && is_string($value)) {
            foreach ($params as $param => $replacement) {
                $value = str_replace(':' . $param, $replacement, $value);
            }
        }
        
        return is_string($value) ? $value : $key;
    }

    /**
     * Alias pour get() - plus court à utiliser
     */
    public static function t(string $key, array $params = []): string
    {
        return self::get($key, $params);
    }

    /**
     * Obtenir toutes les traductions d'une catégorie
     */
    public static function getCategory(string $category): array
    {
        self::getInstance();
        return self::$translations[$category] ?? [];
    }

    /**
     * Vérifier si une traduction existe
     */
    public static function has(string $key): bool
    {
        self::getInstance();
        
        $keys = explode('.', $key);
        $value = self::$translations;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Détecter la langue depuis la session ou le navigateur
     */
    public static function detectLang(): void
    {
        // Priorité : session > navigateur > défaut
        if (Session::isset('lang')) {
            $sessionLang = Session::get('lang');
            if (in_array($sessionLang, ['fr', 'en'])) {
                self::setLang($sessionLang);
                return;
            }
        }

        // Détecter depuis le navigateur
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
        if (in_array($browserLang, ['fr', 'en'])) {
            self::setLang($browserLang);
        } else {
            self::setLang('fr'); // Défaut français
        }
    }
}
