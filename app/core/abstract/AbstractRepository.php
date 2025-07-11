<?php

namespace App\Maxit\Core;


use PDO;
use PDOException;

abstract class AbstractRepository
{
    protected PDO $pdo;
    protected string $table;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    
    public function getTableName(): string{
        //return $this->table;
    }
    protected function getPrimaryKey(): string
    {
        return 'id';
    }
    public function selectAll(): array
    {
        try {
            $sql = "SELECT * FROM " . $this->getTableName();
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la sélection : " . $e->getMessage());
        }
    }

    /**
     * Trouve un enregistrement par son ID
     */
    public function findById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM " . $this->getTableName() . " WHERE " . $this->getPrimaryKey() . " = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la recherche : " . $e->getMessage());
        }
    }

    /**
     * Alias pour findById (pour compatibilité)
     */
    public function selectById(int $id): ?array
    {
        return $this->findById($id);
    }

    /**
     * Insère un nouvel enregistrement
     */
    public function insert(array $data): int
    {
        try {
            $columns = array_keys($data);
            $placeholders = array_map(fn($col) => ":$col", $columns);
            
            $sql = "INSERT INTO " . $this->getTableName() . " (" . implode(', ', $columns) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de l'insertion : " . $e->getMessage());
        }
    }

    /**
     * Met à jour un enregistrement
     */
    public function update(int $id, array $data): bool
    {
        try {
            $setParts = array_map(fn($col) => "$col = :$col", array_keys($data));
            
            $sql = "UPDATE " . $this->getTableName() . " 
                    SET " . implode(', ', $setParts) . " 
                    WHERE " . $this->getPrimaryKey() . " = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    /**
     * Supprime un enregistrement
     */
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM " . $this->getTableName() . " WHERE " . $this->getPrimaryKey() . " = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    /**
     * Compte le nombre total d'enregistrements
     */
    public function count(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM " . $this->getTableName();
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors du comptage : " . $e->getMessage());
        }
    }

    
}
