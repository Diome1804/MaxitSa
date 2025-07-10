<?php


namespace Srx\Service;
use Src\Repository\CompteRepository;
use Srx\Entity\Compte;

class CompteService
{
    private CompteRepository $compteRepo;

    public function __construct()
    {
        $this->compteRepo = new CompteRepository();
    }

    public function getComptesByUserId(int $userId): array
    {
        return $this->compteRepo->findByUserId($userId);
    }
}
