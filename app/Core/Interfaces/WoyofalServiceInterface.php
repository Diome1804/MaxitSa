<?php

namespace App\Core\Interfaces;

interface WoyofalServiceInterface extends ServiceInterface
{
    /**
     * Acheter un code Woyofal
     * 
     * @param string $compteur Numéro du compteur (9-11 chiffres)
     * @param float $montant Montant de l'achat
     * @param int $userId ID de l'utilisateur
     * @return array Résultat de l'achat avec statut et données
     */
    public function acheterCode(string $compteur, float $montant, int $userId): array;

    /**
     * Valider les données d'achat
     * 
     * @param string $compteur Numéro du compteur
     * @param float $montant Montant de l'achat
     * @return array Tableau des erreurs de validation
     */
    public function validerDonneesAchat(string $compteur, float $montant): array;

    /**
     * Vérifier la disponibilité du solde
     * 
     * @param int $userId ID de l'utilisateur
     * @param float $montant Montant requis
     * @return bool True si le solde est suffisant
     */
    public function verifierSoldeDisponible(int $userId, float $montant): bool;

    /**
     * Générer un reçu d'achat
     * 
     * @param array $transactionData Données de la transaction
     * @param array $woyofalResponse Réponse de l'API Woyofal
     * @return array Données du reçu formatées
     */
    public function genererRecu(array $transactionData, array $woyofalResponse): array;

    /**
     * Enregistrer la transaction dans la base de données
     * 
     * @param int $userId ID de l'utilisateur
     * @param float $montant Montant de la transaction
     * @param array $woyofalData Données de la réponse Woyofal
     * @return int|false ID de la transaction créée ou false en cas d'erreur
     */
    public function enregistrerTransaction(int $userId, float $montant, array $woyofalData): int|false;

    /**
     * Obtenir l'historique des achats Woyofal
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre de résultats
     * @return array Historique des achats
     */
    public function getHistoriqueAchats(int $userId, int $limit = 10): array;

    /**
     * Récupérer un reçu par ID de transaction
     * 
     * @param int $transactionId ID de la transaction
     * @param int $userId ID de l'utilisateur (pour sécurité)
     * @return array|null Données du reçu ou null si non trouvé
     */
    public function getRecuParTransaction(int $transactionId, int $userId): ?array;
}
