<?php

namespace App\Core\Interfaces;

interface CompteServiceInterface extends ServiceInterface
{
    public function getSolde(int $userId): float;
    public function createCompteSecondaire(int $userId, string $numero, float $soldeInitial): array;
    public function getComptesByUserId(int $userId): array;
    public function changerComptePrincipal(int $userId, int $nouveauComptePrincipalId): array;
}
