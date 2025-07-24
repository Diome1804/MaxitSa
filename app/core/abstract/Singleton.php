<?php

namespace App\Core\Abstract;

abstract class Singleton
{
    protected static array $instances = [];

    protected function __construct()
    {
        // Constructeur protégé pour empêcher l'instanciation directe
    }

    public static function getInstance(): static
    {
        $class = static::class;
        
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new static();
        }
        
        return static::$instances[$class];
    }

    final public function __clone()
    {
        throw new \Exception("Le clonage d'un singleton n'est pas autorisé");
    }

    final public function __wakeup()
    {
        throw new \Exception("La désérialisation d'un singleton n'est pas autorisée");
    }

    final public function __sleep()
    {
        throw new \Exception("La sérialisation d'un singleton n'est pas autorisée");
    }
}
