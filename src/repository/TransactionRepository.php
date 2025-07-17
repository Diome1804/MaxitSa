<?php

namespace Src\Repository;

use App\Core\Abstract\AbstractRepository;
use PDO;

class TransactionRepository extends AbstractRepository
{
    private static ?TransactionRepository $instance = null;

    public static function getInstance(): TransactionRepository
    {
        if (self::$instance === null) {
            self::$instance = new TransactionRepository();
        }
        return self::$instance;
    }

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
