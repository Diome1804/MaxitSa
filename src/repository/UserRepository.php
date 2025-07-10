<?php

namespace Src\Repository;

use App\Core\Database;
use PDO;

class UserRepository
{
    private static ?UserRepository $instance = null;
    private PDO $db;

    private function __construct()
    {
        $this->db = Database::getInstance();
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
        
        $stmt = $this->db->prepare($sql);
        
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
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($compteData)) {
            return $stmt->fetchColumn();
        }
        
        return null;
    }

    public function findByTelephone(string $telephone): ?array
    {
        $sql = "SELECT * FROM \"user\" WHERE telephone = :telephone";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['telephone' => $telephone]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByNumCarteIdentite(string $numCarte): ?array
    {
        $sql = "SELECT * FROM \"user\" WHERE num_carte_identite = :num_carte";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['num_carte' => $numCarte]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT u.*, t.type as user_type FROM \"user\" u 
                LEFT JOIN typeuser t ON u.type_id = t.id 
                WHERE u.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getCompteByUserId(int $userId): ?array
    {
        $sql = "SELECT * FROM compte WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getTransactionsByUserId(int $userId): array
    {
        $sql = "SELECT t.* FROM transactions t 
                INNER JOIN compte c ON t.compte_id = c.id 
                WHERE c.user_id = :user_id 
                ORDER BY t.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function __clone() {}

    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
