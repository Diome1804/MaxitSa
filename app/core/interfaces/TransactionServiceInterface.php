<?php

namespace App\Core\Interfaces;

interface TransactionServiceInterface extends ServiceInterface
{
    public function getTransactionsByUserId(int $userId): array;
    public function getRecentTransactions(int $userId, int $limit = 10): array;
    public function getLatestTransactions(int $userId, int $limit = 10): array;
    public function getTransactionsWithPagination(int $userId, int $page = 1, int $perPage = 10): array;
    public function getTransactionsWithFilters(int $userId, array $filters = [], int $page = 1, int $perPage = 10): array;
    public function getTransactionTypes(): array;
    public function formatTransactionForDisplay(array $transaction): array;
}
