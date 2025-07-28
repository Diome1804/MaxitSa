<?php

namespace App\Core;

use ReflectionClass;
use ReflectionException;

class ReflectionFactory
{
    private static ?ReflectionFactory $instance = null;
    private array $instances = [];

    private function __construct() {}

    public static function getInstance(): ReflectionFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Créer une instance d'une classe en utilisant la réflexion
     */
    public function create(string $className, array $constructorArgs = []): object
    {
        try {
            $reflection = new ReflectionClass($className);
            
            if (!$reflection->isInstantiable()) {
                throw new \Exception("Cannot instantiate class '{$className}' - it's abstract or an interface");
            }

            $constructor = $reflection->getConstructor();
            
            if ($constructor === null) {
                return $reflection->newInstance();
            }

            return $reflection->newInstanceArgs($constructorArgs);
            
        } catch (ReflectionException $e) {
            throw new \Exception("Failed to create instance of '{$className}': " . $e->getMessage());
        }
    }

    /**
     * Créer une instance en résolvant automatiquement les dépendances
     */
    public function createWithAutoResolve(string $className): object
    {
        try {
            $reflection = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                return $reflection->newInstance();
            }

            $dependencies = [];
            foreach ($constructor->getParameters() as $parameter) {
                $dependencies[] = $this->resolveDependency($parameter, $className);
            }

            return $reflection->newInstanceArgs($dependencies);
            
        } catch (ReflectionException $e) {
            throw new \Exception("Failed to create instance of '{$className}' with auto-resolve: " . $e->getMessage());
        }
    }

    /**
     * Créer une instance singleton ou retourner l'existante
     */
    public function singleton(string $className, array $constructorArgs = []): object
    {
        if (!isset($this->instances[$className])) {
            $this->instances[$className] = $this->create($className, $constructorArgs);
        }
        
        return $this->instances[$className];
    }

    /**
     * Résoudre une dépendance pour un paramètre
     */
    private function resolveDependency(\ReflectionParameter $parameter, string $parentClass): mixed
    {
        $type = $parameter->getType();
        
        if ($type === null) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw new \Exception("Cannot resolve dependency for parameter '{$parameter->getName()}' in class '{$parentClass}'");
        }

        $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : (string)$type;
        
        // Types primitifs
        if (in_array($typeName, ['string', 'int', 'float', 'bool', 'array'])) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw new \Exception("Cannot auto-resolve primitive type '{$typeName}' for parameter '{$parameter->getName()}'");
        }

        // Essayer de résoudre via le conteneur DI
        try {
            $container = DependencyContainer::getInstance();
            
            // Chercher par nom de classe
            foreach ($container->debugConfig() as $serviceId => $className) {
                if ($className === $typeName) {
                    return $container->get($serviceId);
                }
            }
            
            // Si pas trouvé, créer récursivement
            return $this->createWithAutoResolve($typeName);
            
        } catch (\Exception $e) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw new \Exception("Cannot resolve dependency '{$typeName}' for parameter '{$parameter->getName()}': " . $e->getMessage());
        }
    }

    /**
     * Vérifier si une classe existe
     */
    public function classExists(string $className): bool
    {
        return class_exists($className);
    }

    /**
     * Obtenir les informations sur une classe
     */
    public function getClassInfo(string $className): array
    {
        try {
            $reflection = new ReflectionClass($className);
            
            return [
                'name' => $reflection->getName(),
                'shortName' => $reflection->getShortName(),
                'namespace' => $reflection->getNamespaceName(),
                'isAbstract' => $reflection->isAbstract(),
                'isInterface' => $reflection->isInterface(),
                'isInstantiable' => $reflection->isInstantiable(),
                'parentClass' => $reflection->getParentClass() ? $reflection->getParentClass()->getName() : null,
                'interfaces' => $reflection->getInterfaceNames(),
                'methods' => array_map(fn($method) => $method->getName(), $reflection->getMethods()),
                'properties' => array_map(fn($prop) => $prop->getName(), $reflection->getProperties())
            ];
            
        } catch (ReflectionException $e) {
            throw new \Exception("Failed to get class info for '{$className}': " . $e->getMessage());
        }
    }
}
