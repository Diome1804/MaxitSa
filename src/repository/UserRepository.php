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

    public function getCompteByUserId(int $userId): ?array
    {
        // Retourner des données par défaut en attendant la vraie table compte
        return [
            'num_compte' => 'MAX' . str_pad($userId, 7, '0', STR_PAD_LEFT),
            'type' => 'courant',
            'solde' => 0,
            'num_telephone' => 'N/A'
        ];
    }

    public function getTransactionsByUserId(int $userId, int $limit = 10): array
    {
        // Retourner un tableau vide en attendant la vraie table transactions
        return [];
    }

    private function __clone() {}

    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
