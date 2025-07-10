<?php

namespace Src\Repository;

use App\Core\Database;
use PDO;

class UserRepository
{
    private static ?UserRepository $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public static function getInstance(): UserRepository
    {
        if (self::$instance === null) {
            self::$instance = new UserRepository();
        }
        return self::$instance;
    }

    public function create(array $userData): ?int
    {
        $sql = "INSERT INTO \"user\" (nom, prenom, adresse, num_carte_identite, photorecto, photoverso, telephone, password, type_id) 
                VALUES (:nom, :prenom, :adresse, :num_carte_identite, :photorecto, :photoverso, :telephone, :password, :type_id)
                RETURNING id";
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute($userData)) {
            return $stmt->fetchColumn();
        }
        
        return null;
    }

    public function createCompte(array $compteData): ?int
    {
        $sql = "INSERT INTO compte (num_compte, solde, user_id, type, num_telephone) 
                VALUES (:num_compte, :solde, :user_id, :type, :num_telephone)
                RETURNING id";
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute($compteData)) {
            return $stmt->fetchColumn();
        }
        
        return null;
    }

    /**
     * Trouver un utilisateur par téléphone (pour la connexion)
     */
    public function findByTelephone(string $telephone): ?array
    {
        try {
            // ✅ Nom de table PostgreSQL (sans guillemets dans la requête)
            $stmt = $this->pdo->prepare('
                SELECT id, nom, prenom, telephone, password, type_id 
                FROM "user" 
                WHERE telephone = :telephone
            ');
            $stmt->execute(['telephone' => $telephone]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
            
        } catch (\Exception $e) {
            error_log("Erreur findByTelephone: " . $e->getMessage());
            return null;
        }
    }

    public function findByNumCarteIdentite(string $numCarte): ?array
    {
        $sql = "SELECT * FROM \"user\" WHERE num_carte_identite = :num_carte";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['num_carte' => $numCarte]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findById(int $userId): ?array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT id, nom, prenom, telephone, adresse, num_carte_identite 
                FROM "user" 
                WHERE id = :id
            ');
            $stmt->execute(['id' => $userId]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
            
        } catch (\Exception $e) {
            error_log("Erreur findById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Récupérer seulement les 3 colonnes nécessaires
     */
    public function getTransactionsByUserId(int $userId, int $limit = 10): array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT 
                    t.type,
                    t.montant,
                    t.date
                FROM transactions t
                INNER JOIN compte c ON t.compte_id = c.id
                WHERE c.user_id = :user_id
                ORDER BY t.date DESC, t.id DESC
                LIMIT :limit
            ');
        
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (\Exception $e) {
            error_log("Erreur getTransactionsByUserId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ Récupérer le compte d'un utilisateur (PostgreSQL)
     */
    public function getCompteByUserId(int $userId): ?array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT 
                    c.id,
                    c.num_compte,
                    c.solde,
                    c.type,
                    c.date_creation,
                    u.telephone as num_telephone
                FROM compte c
                INNER JOIN "user" u ON c.user_id = u.id
                WHERE c.user_id = :user_id
                LIMIT 1
            ');
        
            $stmt->execute(['user_id' => $userId]);
            $compte = $stmt->fetch(PDO::FETCH_ASSOC);
        
            return $compte ?: null;
        
        } catch (\Exception $e) {
            error_log("Erreur getCompteByUserId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Calculer le solde réel d'un compte basé sur les transactions
     */
    public function calculateSoldeByUserId(int $userId): float
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT 
                    COALESCE(SUM(
                        CASE 
                            WHEN t.type = \'depot\' THEN t.montant
                            WHEN t.type = \'retrait\' THEN -t.montant
                            WHEN t.type = \'transfert\' AND t.montant < 0 THEN t.montant
                            WHEN t.type = \'transfert\' AND t.montant > 0 THEN t.montant
                            ELSE 0
                        END
                    ), 0) as solde_total
                FROM transactions t
                INNER JOIN compte c ON t.compte_id = c.id
                WHERE c.user_id = :user_id
            ');
        
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
            return (float) ($result['solde_total'] ?? 0);
        
        } catch (\Exception $e) {
            error_log("Erreur calculateSoldeByUserId: " . $e->getMessage());
            return 0.0;
        }
    }

    private function __clone() {}

    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
