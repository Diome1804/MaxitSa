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

    public function formatTransactionForDisplay(array $transaction): array
    {
        return [
            'id' => $transaction['id'],
            'type' => $this->getTypeLabel($transaction['type']),
            'montant' => number_format($transaction['montant'], 0, ',', ' ') . ' FCFA',
            'date' => date('d/m/Y', strtotime($transaction['date'])), // ✅ 'date' au lieu de 'date_transaction'
            'numero_compte' => $transaction['num_compte'] ?? '', // ✅ 'num_compte' au lieu de 'numero_compte'
            'css_class' => $this->getTypeClass($transaction['type'])
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
}
