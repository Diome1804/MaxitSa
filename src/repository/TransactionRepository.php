<?php

namespace Src\Repository;

use App\Core\Abstract\AbstractRepository;
use PDO;

class TransactionRepository extends AbstractRepository
{


    public function findByUserId(int $userId): array
    {
        // Union pour récupérer toutes les transactions (anciennes et nouvelles structures)
        $sql = "
            (        

                // hbdshjdshjdshjds
                SELECT t.*, c.num_compte, t.date as date_transaction, 'compte' as source_type
                FROM transactions t 
                INNER JOIN compte c ON t.compte_id = c.id 
                WHERE c.user_id = :user_id AND t.compte_id IS NOT NULL
            )
            UNION ALL
            (
                SELECT t.*, NULL as num_compte, 
                       COALESCE(t.date_creation, t.date) as date_transaction, 
                       'direct' as source_type
                FROM transactions t 
                WHERE t.user_id = :user_id AND t.user_id IS NOT NULL
            )
            ORDER BY date_transaction DESC
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
        // Union pour récupérer toutes les transactions (anciennes et nouvelles structures)
        $sql = "
            (
                SELECT t.*, c.num_compte, t.date as date_transaction, 'compte' as source_type
                FROM transactions t 
                INNER JOIN compte c ON t.compte_id = c.id 
                WHERE c.user_id = :user_id AND t.compte_id IS NOT NULL
            )
            UNION ALL
            (
                SELECT t.*, NULL as num_compte, 
                       COALESCE(t.date_creation, t.date) as date_transaction, 
                       'direct' as source_type
                FROM transactions t 
                WHERE t.user_id = :user_id AND t.user_id IS NOT NULL
            )
            ORDER BY date_transaction DESC 
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
        
        // Construction des conditions WHERE pour chaque partie de l'UNION
        $whereConditions1 = ["c.user_id = :user_id"];
        $whereConditions2 = ["t.user_id = :user_id2"];
        $params = [':user_id' => $userId, ':user_id2' => $userId];
        
        // Filtre par type
        if (!empty($filters['type'])) {
            $whereConditions1[] = "t.type = :type";
            $whereConditions2[] = "t.type = :type2";
            $params[':type'] = $filters['type'];
            $params[':type2'] = $filters['type'];
        }
        
        // Filtre par date de début
        if (!empty($filters['date_debut'])) {
            $whereConditions1[] = "t.date >= :date_debut";
            $whereConditions2[] = "COALESCE(t.date_creation, t.date) >= :date_debut2";
            $params[':date_debut'] = $filters['date_debut'];
            $params[':date_debut2'] = $filters['date_debut'];
        }
        
        // Filtre par date de fin
        if (!empty($filters['date_fin'])) {
            $whereConditions1[] = "t.date <= :date_fin";
            $whereConditions2[] = "COALESCE(t.date_creation, t.date) <= :date_fin2";
            $params[':date_fin'] = $filters['date_fin'];
            $params[':date_fin2'] = $filters['date_fin'];
        }
        
        $whereClause1 = implode(' AND ', $whereConditions1);
        $whereClause2 = implode(' AND ', $whereConditions2);
        
        // Requête pour récupérer les transactions avec filtres (UNION)
        $sql = "
            SELECT * FROM (
                (
                    SELECT t.*, c.num_compte, t.date as date_transaction, 'compte' as source_type
                    FROM transactions t 
                    INNER JOIN compte c ON t.compte_id = c.id 
                    WHERE $whereClause1 AND t.compte_id IS NOT NULL
                )
                UNION ALL
                (
                    SELECT t.*, NULL as num_compte, 
                           COALESCE(t.date_creation, t.date) as date_transaction, 
                           'direct' as source_type
                    FROM transactions t 
                    WHERE $whereClause2 AND t.user_id IS NOT NULL
                )
            ) as all_transactions
            ORDER BY date_transaction DESC 
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
            SELECT COUNT(*) as total FROM (
                (
                    SELECT t.id
                    FROM transactions t 
                    INNER JOIN compte c ON t.compte_id = c.id 
                    WHERE $whereClause1 AND t.compte_id IS NOT NULL
                )
                UNION ALL
                (
                    SELECT t.id
                    FROM transactions t 
                    WHERE $whereClause2 AND t.user_id IS NOT NULL
                )
            ) as all_transactions
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
            $sql = "SELECT DISTINCT type FROM transactions WHERE type IS NOT NULL ORDER BY type";
            $stmt = $this->pdo->query($sql);
            $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Ajouter les types manquants s'ils n'existent pas
            $defaultTypes = ['depot', 'retrait', 'transfert', 'paiement', 'woyofal', 'transfert_sortant', 'transfert_entrant', 'frais_transfert', 'annulation_depot'];
            $allTypes = array_unique(array_merge($types, $defaultTypes));
            sort($allTypes);
            
            return $allTypes;
        } catch (\Exception $e) {
            return ['depot', 'retrait', 'transfert', 'paiement', 'woyofal', 'transfert_sortant', 'transfert_entrant', 'frais_transfert', 'annulation_depot'];
        }
    }

    public function findByUserIdAndType(int $userId, string $type, int $limit = 10): array
    {
        try {
            // Pour les transactions Woyofal, on stocke directement user_id
            if ($type === 'woyofal') {
                $sql = "
                    SELECT * FROM transactions 
                    WHERE user_id = :userId AND type = :type
                    ORDER BY date_creation DESC 
                    LIMIT :limit
                ";
            } else {
                $sql = "
                    SELECT t.*, c.num_compte 
                    FROM transactions t 
                    INNER JOIN compte c ON t.compte_id = c.id 
                    WHERE c.user_id = :userId AND t.type = :type
                    ORDER BY t.date DESC 
                    LIMIT :limit
                ";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erreur findByUserIdAndType: " . $e->getMessage());
            return [];
        }
    }

    public function findByIdAndUserId(int $transactionId, int $userId, string $type = null): ?array
    {
        try {
            if ($type === 'woyofal') {
                $sql = "
                    SELECT * FROM transactions 
                    WHERE id = :transactionId AND user_id = :userId AND type = :type
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':transactionId', $transactionId, PDO::PARAM_INT);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            } else {
                $sql = "
                    SELECT t.*, c.num_compte 
                    FROM transactions t 
                    INNER JOIN compte c ON t.compte_id = c.id 
                    WHERE t.id = :transactionId AND c.user_id = :userId
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':transactionId', $transactionId, PDO::PARAM_INT);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: null;
        } catch (\Exception $e) {
            error_log("Erreur findByIdAndUserId: " . $e->getMessage());
            return null;
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
        try {
            // Pour les transactions Woyofal, on utilise une structure différente
            if (isset($data['type']) && $data['type'] === 'woyofal') {
                $sql = "
                    INSERT INTO transactions (user_id, type, montant, date, reference, statut, details, date_creation) 
                    VALUES (:user_id, :type, :montant, :date, :reference, :statut, :details, :date_creation)
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    'user_id' => $data['user_id'],
                    'type' => $data['type'],
                    'montant' => $data['montant'],
                    'date' => $data['date'],
                    'reference' => $data['reference'],
                    'statut' => $data['statut'],
                    'details' => $data['details'],
                    'date_creation' => $data['date_creation']
                ]);
                
                return $result ? $this->pdo->lastInsertId() : false;
            }
            
            // Pour les transactions de dépôt/transfert avec nouvelles colonnes
            if (isset($data['expediteur_id']) || isset($data['description']) || isset($data['reference'])) {
                $sql = "
                    INSERT INTO transactions (compte_id, type, montant, date, expediteur_id, statut, description, reference) 
                    VALUES (:compte_id, :type, :montant, :date, :expediteur_id, :statut, :description, :reference)
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $result = $stmt->execute([
                    'compte_id' => $data['compte_id'] ?? null,
                    'type' => $data['type'],
                    'montant' => $data['montant'],
                    'date' => $data['date'] ?? date('Y-m-d H:i:s'),
                    'expediteur_id' => $data['expediteur_id'] ?? null,
                    'statut' => $data['statut'] ?? 'success',
                    'description' => $data['description'] ?? null,
                    'reference' => $data['reference'] ?? null
                ]);
                
                return $result ? $this->pdo->lastInsertId() : false;
            }
            
            // Pour les autres transactions, utiliser l'ancienne méthode
            return $this->create($data);
        } catch (\Exception $e) {
            error_log("Erreur insert transaction: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus(int $transactionId, string $status): bool
    {
        try {
            $sql = "UPDATE transactions SET statut = :status WHERE id = :transaction_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'status' => $status,
                'transaction_id' => $transactionId
            ]);
        } catch (\Exception $e) {
            error_log("Erreur updateStatus: " . $e->getMessage());
            return false;
        }
    }

    public function update() {}
    public function delete() {}
    public function selectById() {}
    public function selectBy(array $filter) {}
}
