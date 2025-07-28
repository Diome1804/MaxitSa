<?php

namespace App\Core\Interfaces;

interface DepotServiceInterface extends ServiceInterface
{
    /**
     * Effectuer un dépôt sur un compte
     * 
     * @param int $compteId ID du compte destinataire
     * @param float $montant Montant du dépôt
     * @param int $expediteurId ID de l'expéditeur
     * @param string $type Type de dépôt (depot, transfert)
     * @return array Résultat de l'opération
     */
    public function effectuerDepot(int $compteId, float $montant, int $expediteurId, string $type = 'depot'): array;

    /**
     * Effectuer un transfert entre comptes
     * 
     * @param int $compteSourceId ID du compte source
     * @param int $compteDestinationId ID du compte destination  
     * @param float $montant Montant du transfert
     * @param int $userId ID de l'utilisateur qui effectue le transfert
     * @return array Résultat de l'opération
     */
    public function effectuerTransfert(int $compteSourceId, int $compteDestinationId, float $montant, int $userId): array;

    /**
     * Calculer les frais de transfert
     * 
     * @param string $typeSource Type du compte source
     * @param string $typeDestination Type du compte destination
     * @param float $montant Montant du transfert
     * @return float Montant des frais
     */
    public function calculerFraisTransfert(string $typeSource, string $typeDestination, float $montant): float;

    /**
     * Annuler une transaction de dépôt
     * 
     * @param int $transactionId ID de la transaction
     * @param int $userId ID de l'utilisateur
     * @return array Résultat de l'opération
     */
    public function annulerDepot(int $transactionId, int $userId): array;

    /**
     * Vérifier si une transaction peut être annulée
     * 
     * @param int $transactionId ID de la transaction
     * @param int $userId ID de l'utilisateur
     * @return bool True si la transaction peut être annulée
     */
    public function peutAnnulerTransaction(int $transactionId, int $userId): bool;

    /**
     * Obtenir les transactions de dépôt annulables pour un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Liste des transactions annulables
     */
    public function getTransactionsAnnulables(int $userId): array;
}
