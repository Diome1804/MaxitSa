<?php

namespace App\Core;

use App\Core\Abstract\AbstractController;
use App\Core\Abstract\AbstractRepository;
use App\Core\Abstract\AbstractEntity;
use App\Core\Database;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Router;

use Src\Repository\UserRepository;
use Src\Repository\CompteRepository;
use Src\Repository\TransactionRepository;

use Src\Service\SecurityService;
use Src\Service\CompteService;
use Src\Service\TransactionService;

class App
{
    private static array $container = [];
    private static bool $initialized = false;

    private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        $dependencies = [
            'core' => [
                'database' => Database::class,
                'session' => Session::class,
                'validator' => Validator::class,
                'router' => Router::class,
                'FileUpload' => FileUpload::class,
            ],
            'abstract' => [
                'abstractRepo' => AbstractRepository::class,
                'abstractController' => AbstractController::class,
                'abstractEntity' => AbstractEntity::class,
            ],
            'services' => [
                'securityServ' => SecurityService::class,
                'compteServ' => CompteService::class,
                'transactionServ' => TransactionService::class,
            ],
            'repository' => [
                'userRepo' => UserRepository::class,
                'compteRepo' => CompteRepository::class,
                'transactionRepo' => TransactionRepository::class,
            ]
        ];

        foreach ($dependencies as $category => $services) {
            foreach ($services as $key => $class) {
                self::$container[$category][$key] = fn() => $class::getInstance();
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
}
