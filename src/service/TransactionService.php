<?php

namespace Src\Service;

use Src\Repository\TransactionRepository;
use Src\Repository\CompteRepository;
use App\Core\App;

class TransactionService
{
    private TransactionRepository $transactionRepository;
    private CompteRepository $compteRepository;
    private static ?TransactionService $instance = null;

    public static function getInstance(): TransactionService
    {
        if (self::$instance === null) {
            self::$instance = new TransactionService();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->transactionRepository = App::getDependency('repository', 'transactionRepo');
        $this->compteRepository = App::getDependency('repository', 'compteRepo');
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
        return [
            'id' => $transaction['id'],
            'type' => $this->getTypeLabel($transaction['type']),
            'montant' => number_format($transaction['montant'], 0, ',', ' ') . ' FCFA',
            'date' => date('d/m/Y', strtotime($transaction['date'])),
            'numero_compte' => $transaction['num_compte'] ?? '', 
            'css_class' => $this->getTypeClass($transaction['type']),
            'badge_class' => $this->getTypeBadgeClass($transaction['type'])
        ];
    }

    private function getTypeLabel(string $type): string
    {
        return match($type) {
            'paiement' => 'Paiement',
            'transfert' => 'Transfert',
            'depot' => 'Dépôt',
            'retrait' => 'Retrait',
            default => ucfirst($type)
        };
    }

    private function getTypeClass(string $type): string
    {
        return match($type) {
            'paiement' => 'text-green-600',
            'depot' => 'text-green-600',
            'retrait' => 'text-red-600',
            'transfert' => 'text-blue-600',
            default => 'text-gray-600'
        };
    }

    private function getTypeBadgeClass(string $type): string
    {
        return match($type) {
            'paiement' => 'bg-green-100 text-green-800',
            'depot' => 'bg-green-100 text-green-800',
            'retrait' => 'bg-red-100 text-red-800',
            'transfert' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
