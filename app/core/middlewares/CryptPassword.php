<?php

namespace App\Core\Middlewares;

class CryptPassword
{
    /**
     * Hasher un mot de passe
     * 
     * @param string $password Le mot de passe à hasher
     * @param array $options Options pour le hashage (coût, etc.)
     * @return string Le mot de passe hashé
     */
    public function __invoke(string $password, array $options = []): string
    {
        // Utiliser PASSWORD_DEFAULT pour suivre les meilleures pratiques PHP
        return password_hash($password, PASSWORD_DEFAULT, $options);
    }

    /**
     * Vérifier un mot de passe
     * 
     * @param string $password Le mot de passe en clair
     * @param string $hash Le hash à vérifier
     * @return bool True si le mot de passe correspond
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Vérifier si un hash doit être rehashé
     * 
     * @param string $hash Le hash à vérifier
     * @param array $options Options pour le rehashage
     * @return bool True si le hash doit être rehashé
     */
    public function needsRehash(string $hash, array $options = []): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT, $options);
    }
}