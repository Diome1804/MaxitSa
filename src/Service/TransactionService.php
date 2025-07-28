<?php

namespace Src\Service;

use Src\Repository\TransactionRepository;
use Src\Repository\CompteRepository;
use App\Core\Interfaces\TransactionServiceInterface;

class TransactionService implements TransactionServiceInterface
{
    private TransactionRepository $transactionRepository;
    private CompteRepository $compteRepository;

    public function __construct(TransactionRepository $transactionRepository, CompteRepository $compteRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->compteRepository = $compteRepository;
    }

    public function getTransactionsByUserId(int $userId): array
    {
        try {
            return $this->transactionRepository->findByUserId($userId);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getRecentTransactions(int $userId, int $limit = 10): array
    {
        try {
            return $this->transactionRepository->findRecentTransactions($userId, $limit);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getLatestTransactions(int $userId, int $limit = 10): array
    {
        try {
            $transactions = $this->transactionRepository->findRecentTransactions($userId, $limit);
            
            // Formater les transactions pour l'affichage
            $formattedTransactions = [];
            foreach ($transactions as $transaction) {
                $formattedTransactions[] = $this->formatTransactionForDisplay($transaction);
            }
            
            return $formattedTransactions;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getTransactionsWithPagination(int $userId, int $page = 1, int $perPage = 10): array
    {
        try {
            $result = $this->transactionRepository->findTransactionsWithPagination($userId, $page, $perPage);
            
            // Formater les transactions pour l'affichage
            $formattedTransactions = [];
            foreach ($result['transactions'] as $transaction) {
                $formattedTransactions[] = $this->formatTransactionForDisplay($transaction);
            }
            
            return [
                'transactions' => $formattedTransactions,
                'pagination' => $result['pagination']
            ];
        } catch (\Exception $e) {
            return [
                'transactions' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                    'total_pages' => 0,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ];
        }
    }

    public function getTransactionsWithFilters(int $userId, array $filters = [], int $page = 1, int $perPage = 10): array
    {
        try {
            $result = $this->transactionRepository->findTransactionsWithFilters($userId, $filters, $page, $perPage);
            
            // Formater les transactions pour l'affichage
            $formattedTransactions = [];
            foreach ($result['transactions'] as $transaction) {
                $formattedTransactions[] = $this->formatTransactionForDisplay($transaction);
            }
            
            return [
                'transactions' => $formattedTransactions,
                'pagination' => $result['pagination']
            ];
        } catch (\Exception $e) {
            return [
                'transactions' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                    'total_pages' => 0,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ];
        }
    }

    public function getTransactionTypes(): array
    {
        return $this->transactionRepository->getTransactionTypes();
    }

    public function formatTransactionForDisplay(array $transaction): array
    {
        // Utiliser la bonne date selon le type de transaction
        $date = $transaction['date_transaction'] ?? $transaction['date'] ?? $transaction['date_creation'] ?? date('Y-m-d');
        
        return [
            'id' => $transaction['id'],
            'type' => $this->getTypeLabel($transaction['type']),
            'montant' => number_format($transaction['montant'], 0, ',', ' ') . ' FCFA',
            'date' => date('d/m/Y', strtotime($date)),
            'numero_compte' => $transaction['num_compte'] ?? 'N/A', 
            'css_class' => $this->getTypeClass($transaction['type']),
            'badge_class' => $this->getTypeBadgeClass($transaction['type']),
            'description' => $transaction['description'] ?? '',
            'reference' => $transaction['reference'] ?? '',
            'statut' => $transaction['statut'] ?? 'success'
        ];
    }

    private function getTypeLabel(string $type): string
    {
        return match($type) {
            'paiement' => 'Paiement',
            'transfert' => 'Transfert',
            'depot' => 'Dépôt',
            'retrait' => 'Retrait',
            'woyofal' => 'Achat Woyofal',
            'transfert_sortant' => 'Transfert sortant',
            'transfert_entrant' => 'Transfert entrant',
            'frais_transfert' => 'Frais de transfert',
            'annulation_depot' => 'Annulation dépôt',
            default => ucfirst($type)
        };
    }

    private function getTypeClass(string $type): string
    {
        return match($type) {
            'paiement' => 'text-green-600',
            'depot' => 'text-green-600',
            'transfert_entrant' => 'text-green-600',
            'retrait' => 'text-red-600',
            'transfert_sortant' => 'text-red-600',
            'frais_transfert' => 'text-red-600',
            'annulation_depot' => 'text-red-600',
            'transfert' => 'text-blue-600',
            'woyofal' => 'text-yellow-600',
            default => 'text-gray-600'
        };
    }

    private function getTypeBadgeClass(string $type): string
    {
        return match($type) {
            'paiement' => 'bg-green-100 text-green-800',
            'depot' => 'bg-green-100 text-green-800',
            'transfert_entrant' => 'bg-green-100 text-green-800',
            'retrait' => 'bg-red-100 text-red-800',
            'transfert_sortant' => 'bg-red-100 text-red-800',
            'frais_transfert' => 'bg-red-100 text-red-800',
            'annulation_depot' => 'bg-red-100 text-red-800',
            'transfert' => 'bg-blue-100 text-blue-800',
            'woyofal' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
