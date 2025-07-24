<?php

namespace Src\Repository;

use App\Core\Abstract\AbstractRepository;
use PDO;

class TransactionRepository extends AbstractRepository
{


    public function findByUserId(int $userId): array
    {
        $sql = "
            SELECT t.*, c.num_compte 
            FROM transactions t 
            INNER JOIN compte c ON t.compte_id = c.id 
            WHERE c.user_id = :user_id 
            ORDER BY t.date DESC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCompteId(int $compteId): array
    {
        $sql = "
            SELECT * FROM transactions 
            WHERE compte_id = :compte_id 
            ORDER BY date DESC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['compte_id' => $compteId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findRecentTransactions(int $userId, int $limit = 10): array
    {
        $sql = "
            SELECT t.*, c.num_compte 
            FROM transactions t 
            INNER JOIN compte c ON t.compte_id = c.id 
            WHERE c.user_id = :user_id 
            ORDER BY t.date DESC 
            LIMIT :limit
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findTransactionsWithPagination(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Requête pour récupérer les transactions avec pagination
        $sql = "
            SELECT t.*, c.num_compte 
            FROM transactions t 
            INNER JOIN compte c ON t.compte_id = c.id 
            WHERE c.user_id = :user_id 
            ORDER BY t.date DESC 
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Requête pour compter le total des transactions
        $countSql = "
            SELECT COUNT(*) as total 
            FROM transactions t 
            INNER JOIN compte c ON t.compte_id = c.id 
            WHERE c.user_id = :user_id
        ";
        
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $countStmt->execute();
        
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($total / $perPage);
        
        return [
            'transactions' => $transactions,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];
    }

    public function findTransactionsWithFilters(int $userId, array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $whereConditions = ["c.user_id = :user_id"];
        $params = [':user_id' => $userId];
        
        // Filtre par type
        if (!empty($filters['type'])) {
            $whereConditions[] = "t.type = :type";
            $params[':type'] = $filters['type'];
        }
        
        // Filtre par date de début
        if (!empty($filters['date_debut'])) {
            $whereConditions[] = "t.date >= :date_debut";
            $params[':date_debut'] = $filters['date_debut'];
        }
        
        // Filtre par date de fin
        if (!empty($filters['date_fin'])) {
            $whereConditions[] = "t.date <= :date_fin";
            $params[':date_fin'] = $filters['date_fin'];
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Requête pour récupérer les transactions avec filtres
        $sql = "
            SELECT t.*, c.num_compte 
            FROM transactions t 
            INNER JOIN compte c ON t.compte_id = c.id 
            WHERE $whereClause
            ORDER BY t.date DESC 
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Requête pour compter le total avec filtres
        $countSql = "
            SELECT COUNT(*) as total 
            FROM transactions t 
            INNER JOIN compte c ON t.compte_id = c.id 
            WHERE $whereClause
        ";
        
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($total / $perPage);
        
        return [
            'transactions' => $transactions,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];
    }

    public function getTransactionTypes(): array
    {
        try {
            $sql = "SELECT DISTINCT type FROM transactions ORDER BY type";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            return ['depot', 'retrait', 'transfert', 'paiement'];
        }
    }

    public function create(array $data): bool
    {
        $sql = "
            INSERT INTO transactions (compte_id, type, montant, date) 
            VALUES (:compte_id, :type, :montant, :date)
        ";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            'compte_id' => $data['compte_id'],
            'type' => $data['type'],
            'montant' => $data['montant'],
            'date' => $data['date'] ?? date('Y-m-d')
        ]);
    }

    // Méthodes abstraites obligatoires
    public function selectAll()
    {
        $sql = "SELECT * FROM transactions ORDER BY date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(array $data)
    {
        return $this->create($data);
    }

    public function update() {}
    public function delete() {}
    public function selectById() {}
    public function selectBy(array $filter) {}
}
