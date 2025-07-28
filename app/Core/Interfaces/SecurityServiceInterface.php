<?php

namespace App\Core\Interfaces;

use Src\Entity\User;

interface SecurityServiceInterface extends ServiceInterface
{
    public function creerClientAvecComptePrincipal(array $userData, float $soldeInitial = 0.0): int|false;
    public function authenticate(array $data): ?User;
    public function login(string $numero, string $password): ?User;
}
