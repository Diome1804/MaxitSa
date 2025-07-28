<?php

namespace Src\Service;

use App\Core\Interfaces\DepotServiceInterface;
use App\Core\Interfaces\CompteServiceInterface;
use Src\Repository\TransactionRepository;
use Src\Repository\CompteRepository;
use App\Core\ReflectionFactory;
use App\Core\Lang;

class DepotService implements DepotServiceInterface
{
    private CompteServiceInterface $compteService;
    private TransactionRepository $transactionRepository;
    private CompteRepository $compteRepository;
    private ReflectionFactory $factory;
    
    // Frais de transfert: 0.08% du montant, plafonné à 5000 FCFA
    private const TAUX_FRAIS_TRANSFERT = 0.0008; // 0.08%
    private const PLAFOND_FRAIS_TRANSFERT = 5000;

    public function __construct(
        CompteServiceInterface $compteService,
        TransactionRepository $transactionRepository,
        CompteRepository $compteRepository,
        ReflectionFactory $factory
    ) {
        $this->compteService = $compteService;
        $this->transactionRepository = $transactionRepository;
        $this->compteRepository = $compteRepository;
        $this->factory = $factory;
    }

    public function effectuerDepot(int $compteId, float $montant, int $expediteurId, string $type = 'depot'): array
    {
        try {
            // Validation du montant
            if ($montant <= 0) {
                return [
                    'success' => false,
                    'message' => 'Le montant doit être supérieur à 0',
                    'errors' => ['montant' => 'Montant invalide']
                ];
            }

            // Vérifier que le compte existe
            $compte = $this->compteRepository->findById($compteId);
            if (!$compte) {
                return [
                    'success' => false,
                    'message' => 'Compte destinataire introuvable',
                    'errors' => ['compte' => 'Compte invalide']
                ];
            }

            // Créditer le compte
            $nouveauSolde = $compte['solde'] + $montant;
            $updateResult = $this->compteService->updateSoldeCompte($compteId, $nouveauSolde);

            if (!$updateResult) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du solde'
                ];
            }

            // Enregistrer la transaction
            $transactionData = [
                'compte_id' => $compteId,
                'type' => $type,
                'montant' => $montant,
                'date' => date('Y-m-d H:i:s'),
                'expediteur_id' => $expediteurId,
                'statut' => 'success',
                'description' => ucfirst($type) . ' de ' . number_format($montant, 0, ',', ' ') . ' FCFA'
            ];

            $transactionId = $this->transactionRepository->insert($transactionData);

            if (!$transactionId) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'enregistrement de la transaction'
                ];
            }

            return [
                'success' => true,
                'message' => ucfirst($type) . ' effectué avec succès',
                'data' => [
                    'transaction_id' => $transactionId,
                    'nouveau_solde' => $nouveauSolde,
                    'montant' => $montant,
                    'compte_numero' => $compte['num_compte']
                ]
            ];

        } catch (\Exception $e) {
            error_log("Erreur dépôt: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur technique lors du dépôt',
                'errors' => ['system' => $e->getMessage()]
            ];
        }
    }

    public function effectuerTransfert(int $compteSourceId, int $compteDestinationId, float $montant, int $userId): array
    {
        try {
            // Vérifications de base
            if ($compteSourceId === $compteDestinationId) {
                return [
                    'success' => false,
                    'message' => 'Le compte source et destination ne peuvent pas être identiques'
                ];
            }

            // Récupérer les comptes
            $compteSource = $this->compteRepository->findById($compteSourceId);
            $compteDestination = $this->compteRepository->findById($compteDestinationId);

            if (!$compteSource || !$compteDestination) {
                return [
                    'success' => false,
                    'message' => 'Un des comptes est introuvable'
                ];
            }

            // Vérifier que l'utilisateur a accès au compte source
            if ($compteSource['user_id'] !== $userId) {
                return [
                    'success' => false,
                    'message' => 'Vous n\'avez pas accès à ce compte'
                ];
            }

            // Calculer les frais
            $frais = $this->calculerFraisTransfert(
                $compteSource['type'], 
                $compteDestination['type'], 
                $montant
            );

            $montantTotal = $montant + $frais;

            // Vérifier le solde
            if ($compteSource['solde'] < $montantTotal) {
                return [
                    'success' => false,
                    'message' => 'Solde insuffisant (montant + frais: ' . number_format($montantTotal, 0, ',', ' ') . ' FCFA)'
                ];
            }

            // Effectuer le transfert
            // 1. Débiter le compte source
            $nouveauSoldeSource = $compteSource['solde'] - $montantTotal;
            $debitResult = $this->compteService->updateSoldeCompte($compteSourceId, $nouveauSoldeSource);

            if (!$debitResult) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du débit du compte source'
                ];
            }

            // 2. Créditer le compte destination
            $nouveauSoldeDestination = $compteDestination['solde'] + $montant;
            $creditResult = $this->compteService->updateSoldeCompte($compteDestinationId, $nouveauSoldeDestination);

            if (!$creditResult) {
                // Annuler le débit en cas d'erreur
                $this->compteService->updateSoldeCompte($compteSourceId, $compteSource['solde']);
                return [
                    'success' => false,
                    'message' => 'Erreur lors du crédit du compte destination'
                ];
            }

            // 3. Enregistrer les transactions
            $reference = 'TRF' . date('YmdHis') . rand(100, 999);

            // Transaction de débit
            $transactionDebit = [
                'compte_id' => $compteSourceId,
                'type' => 'transfert_sortant',
                'montant' => -$montant,
                'date' => date('Y-m-d H:i:s'),
                'reference' => $reference,
                'statut' => 'success',
                'description' => 'Transfert vers ' . $compteDestination['num_compte']
            ];

            // Transaction de crédit
            $transactionCredit = [
                'compte_id' => $compteDestinationId,
                'type' => 'transfert_entrant',
                'montant' => $montant,
                'date' => date('Y-m-d H:i:s'),
                'reference' => $reference,
                'statut' => 'success',
                'description' => 'Transfert depuis ' . $compteSource['num_compte']
            ];

            // Transaction de frais (si applicable)
            if ($frais > 0) {
                $transactionFrais = [
                    'compte_id' => $compteSourceId,
                    'type' => 'frais_transfert',
                    'montant' => -$frais,
                    'date' => date('Y-m-d H:i:s'),
                    'reference' => $reference,
                    'statut' => 'success',
                    'description' => 'Frais de transfert'
                ];
                $this->transactionRepository->insert($transactionFrais);
            }

            $transactionDebitId = $this->transactionRepository->insert($transactionDebit);
            $transactionCreditId = $this->transactionRepository->insert($transactionCredit);

            return [
                'success' => true,
                'message' => 'Transfert effectué avec succès',
                'data' => [
                    'reference' => $reference,
                    'montant' => $montant,
                    'frais' => $frais,
                    'montant_total' => $montantTotal,
                    'compte_source' => $compteSource['num_compte'],
                    'compte_destination' => $compteDestination['num_compte'],
                    'nouveau_solde_source' => $nouveauSoldeSource
                ]
            ];

        } catch (\Exception $e) {
            error_log("Erreur transfert: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur technique lors du transfert',
                'errors' => ['system' => $e->getMessage()]
            ];
        }
    }

    public function calculerFraisTransfert(string $typeSource, string $typeDestination, float $montant): float
    {
        // Frais uniquement pour les transferts entre deux comptes principaux
        if ($typeSource === 'ComptePrincipal' && $typeDestination === 'ComptePrincipal') {
            $frais = $montant * self::TAUX_FRAIS_TRANSFERT;
            return min($frais, self::PLAFOND_FRAIS_TRANSFERT);
        }

        return 0; // Pas de frais pour les autres types de transfert
    }

    public function annulerDepot(int $transactionId, int $userId): array
    {
        try {
            // Vérifier si la transaction peut être annulée
            if (!$this->peutAnnulerTransaction($transactionId, $userId)) {
                return [
                    'success' => false,
                    'message' => 'Cette transaction ne peut pas être annulée'
                ];
            }

            // Récupérer la transaction
            $transaction = $this->transactionRepository->findByIdAndUserId($transactionId, $userId);
            if (!$transaction) {
                return [
                    'success' => false,
                    'message' => 'Transaction introuvable'
                ];
            }

            // Récupérer le compte
            $compte = $this->compteRepository->findById($transaction['compte_id']);
            if (!$compte) {
                return [
                    'success' => false,
                    'message' => 'Compte introuvable'
                ];
            }

            // Vérifier que le montant n'a pas été retiré (solde suffisant)
            if ($compte['solde'] < $transaction['montant']) {
                return [
                    'success' => false,
                    'message' => 'Le montant a déjà été partiellement retiré, annulation impossible'
                ];
            }

            // Effectuer l'annulation
            $nouveauSolde = $compte['solde'] - $transaction['montant'];
            $updateResult = $this->compteService->updateSoldeCompte($compte['id'], $nouveauSolde);

            if (!$updateResult) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'annulation'
                ];
            }

            // Marquer la transaction comme annulée
            $this->transactionRepository->updateStatus($transactionId, 'cancelled');

            // Créer une transaction d'annulation
            $transactionAnnulation = [
                'compte_id' => $compte['id'],
                'type' => 'annulation_depot',
                'montant' => -$transaction['montant'],
                'date' => date('Y-m-d H:i:s'),
                'reference' => 'ANN' . $transactionId,
                'statut' => 'success',
                'description' => 'Annulation dépôt #' . $transactionId
            ];

            $this->transactionRepository->insert($transactionAnnulation);

            return [
                'success' => true,
                'message' => 'Dépôt annulé avec succès',
                'data' => [
                    'montant_annule' => $transaction['montant'],
                    'nouveau_solde' => $nouveauSolde
                ]
            ];

        } catch (\Exception $e) {
            error_log("Erreur annulation dépôt: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur technique lors de l\'annulation'
            ];
        }
    }

    public function peutAnnulerTransaction(int $transactionId, int $userId): bool
    {
        try {
            $transaction = $this->transactionRepository->findByIdAndUserId($transactionId, $userId);
            
            if (!$transaction) {
                return false;
            }

            // Vérifier le type de transaction (seulement les dépôts)
            if (!in_array($transaction['type'], ['depot', 'transfert_entrant'])) {
                return false;
            }

            // Vérifier le statut
            if ($transaction['statut'] !== 'success') {
                return false;
            }

            // Vérifier la date (par exemple, annulation possible dans les 24h)
            $dateTransaction = new \DateTime($transaction['date']);
            $maintenant = new \DateTime();
            $diff = $maintenant->diff($dateTransaction);
            
            if ($diff->days >= 1) { // Plus de 24h
                return false;
            }

            return true;

        } catch (\Exception $e) {
            error_log("Erreur vérification annulation: " . $e->getMessage());
            return false;
        }
    }

    public function getTransactionsAnnulables(int $userId): array
    {
        try {
            $transactions = $this->transactionRepository->findByUserId($userId);
            $annulables = [];

            foreach ($transactions as $transaction) {
                if ($this->peutAnnulerTransaction($transaction['id'], $userId)) {
                    $annulables[] = $transaction;
                }
            }

            return $annulables;

        } catch (\Exception $e) {
            error_log("Erreur récupération transactions annulables: " . $e->getMessage());
            return [];
        }
    }
}
