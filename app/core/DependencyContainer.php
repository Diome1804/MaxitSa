<?php

namespace App\Core;

use App\Core\YamlParser;
use ReflectionClass;
use ReflectionParameter;

class DependencyContainer
{
    private static ?DependencyContainer $instance = null;
    private array $services = [];
    private array $instances = [];
    private array $config = [];

    private function __construct()
    {
        $this->loadConfiguration();
    }

    public static function getInstance(): DependencyContainer
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfiguration(): void
    {
        $configPath = __DIR__ . '/../config/services.yml';
        $this->config = YamlParser::parseFile($configPath);
    }

    public function get(string $serviceId): object
    {
        if (isset($this->instances[$serviceId])) {
            return $this->instances[$serviceId];
        }

        $className = $this->resolveClassName($serviceId);
        $this->instances[$serviceId] = $this->createInstance($className);
        
        return $this->instances[$serviceId];
    }

    private function resolveClassName(string $serviceId): string
    {
        if (isset($this->config[$serviceId])) {
            return $this->config[$serviceId];
        }
        
        throw new \Exception("Service '{$serviceId}' not found in configuration");
    }

    private function createInstance(string $className): object
    {
        $reflection = new ReflectionClass($className);
        
        // Vérifier si la classe a une méthode getInstance (Singleton)
        if ($reflection->hasMethod('getInstance')) {
            $getInstanceMethod = $reflection->getMethod('getInstance');
            if ($getInstanceMethod->isStatic() && $getInstanceMethod->isPublic()) {
                return $className::getInstance();
            }
        }
        
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        // Vérifier si le constructeur est privé (Singleton)
        if ($constructor->isPrivate()) {
            throw new \Exception("Cannot instantiate class '{$className}' with private constructor. Use getInstance() method or make constructor public.");
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $dependencies[] = $this->resolveDependency($parameter);
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    private function resolveDependency(ReflectionParameter $parameter): object
    {
        $type = $parameter->getType();
        
        if ($type === null) {
            throw new \Exception("Cannot resolve dependency for parameter '{$parameter->getName()}'");
        }

        $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : (string)$type;
        
        // Priorité aux services spécialisés (les plus longs en premier)
        $services = $this->config;
        uksort($services, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        
        // Chercher le service par nom de classe
        foreach ($services as $serviceId => $className) {
            if ($className === $typeName) {
                return $this->get($serviceId);
            }
        }

        throw new \Exception("Cannot resolve dependency for type '{$typeName}'");
    }

    public function register(string $serviceId, string $className): void
    {
        $this->services[$serviceId] = $className;
    }
}
