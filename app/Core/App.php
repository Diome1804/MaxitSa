<?php

namespace App\Core;

use App\Core\YamlParser;
use App\Core\DependencyContainer;

class App
{
    private static array $container = [];
    private static bool $initialized = false;
    private static ?DependencyContainer $diContainer = null;

    private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$diContainer = DependencyContainer::getInstance();
        self::$initialized = true;
    }

    public static function getService(string $serviceId)
    {
        self::initialize();
        return self::$diContainer->get($serviceId);
    }
    
    /**
     * @deprecated Use getService() instead
     */
    public static function getDependency(string $category, string $key)
    {
        // Mappage temporaire pour la compatibilité
        $serviceMap = [
            'core' => [
                'session' => 'session',
                'database' => 'database',
                'validator' => 'validator',
                'FileUpload' => 'fileUpload'
            ],
            'services' => [
                'transactionServ' => 'transactionServ',
                'compteServ' => 'compteServ',
                'securityServ' => 'securityServ',
                'appdafServ' => 'appdafServ'
            ],
            'repository' => [
                'compteRepo' => 'compteRepo',
                'userRepo' => 'userRepo',
                'transactionRepo' => 'transactionRepo'
            ]
        ];

        if (isset($serviceMap[$category][$key])) {
            return self::getService($serviceMap[$category][$key]);
        }

        throw new \Exception("Dependency '{$key}' not found in category '{$category}'");
    }

    // Méthode de débogage pour vérifier les dépendances chargées
    public static function debugDependencies(): array
    {
        self::initialize();
        return self::$container;
    }
}
