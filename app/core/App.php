<?php

namespace App\Core;

use App\Core\YamlParser;

class App
{
    private static array $container = [];
    private static bool $initialized = false;

    private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        // Charger les dépendances depuis le fichier services.yml
        $configPath = __DIR__ . '/../config/services.yml';
        $dependencies = YamlParser::parseFile($configPath);

        foreach ($dependencies as $category => $services) {
            foreach ($services as $key => $className) {
                // Convertir le nom de classe en closure qui appelle getInstance()
                self::$container[$category][$key] = fn() => $className::getInstance();
            }
        }

        self::$initialized = true;
    }

    
    public static function getDependency(string $category, string $key)
    {
        self::initialize();

        if (!isset(self::$container[$category][$key])) {
            throw new \Exception("Dependency '{$key}' not found in category '{$category}'");
        }

        $dependency = self::$container[$category][$key];


        if (is_callable($dependency)) {
            self::$container[$category][$key] = $dependency();
        }

        return self::$container[$category][$key];
    }

    // Méthode de débogage pour vérifier les dépendances chargées
    public static function debugDependencies(): array
    {
        self::initialize();
        return self::$container;
    }
}
