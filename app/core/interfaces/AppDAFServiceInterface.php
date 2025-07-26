<?php

namespace App\Core\Interfaces;

interface AppDAFServiceInterface extends ServiceInterface
{
    /**
     * Recherche un citoyen par son numéro CNI dans l'API AppDAF
     * 
     * @param string $nci Numéro de carte d'identité (13 chiffres)
     * @return array|null Données du citoyen ou null si non trouvé
     * @throws \Exception En cas d'erreur de communication avec l'API
     */
    public function rechercherCitoyenParCNI(string $nci): ?array;

    /**
     * Vérifie si l'API AppDAF est disponible
     * 
     * @return bool
     */
    public function isAPIDisponible(): bool;
}
