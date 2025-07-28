<?php

namespace Src\Repository;
use App\Core\Abstract\AbstractRepository;
use PDO;

class CompteRepository extends AbstractRepository{

    private string $table = 'compte';

    

    public function __construct(){
        parent::__construct();
    }

     public function selectAll(){}

    
     // Méthode pour récupérer le solde principal d'un utilisateur par son ID
    public function getSoldeByUserId(int $userId): float
    {
        try {
            $sql = "SELECT solde FROM $this->table WHERE user_id = :user_id AND type = 'ComptePrincipal' LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (float) $result['solde'] : 0.0;
        } catch (\Exception $e) {
                return 0.0;
        }
    }

         public function insert(array $data)
    {
         try {
             $sql = "INSERT INTO $this->table (user_id, num_compte, solde, type, num_telephone) VALUES (:user_id, :num_compte, :solde, :type, :num_telephone)";
             $stmt = $this->pdo->prepare($sql);
             return $stmt->execute([
                 ':user_id' => $data['user_id'],
                 ':num_compte' => $data['num_compte'],
                 ':solde' => $data['solde'],
                 ':type' => $data['type'],
                 ':num_telephone' => $data['num_telephone']
             ]);
         } catch (\Exception $e) {
             return false;
         }
     }

     public function updateSolde(int $userId, float $nouveauSolde): bool
     {
         try {
             $sql = "UPDATE $this->table SET solde = :solde WHERE user_id = :user_id AND type = 'ComptePrincipal'";
             $stmt = $this->pdo->prepare($sql);
             return $stmt->execute([
                 ':solde' => $nouveauSolde,
                 ':user_id' => $userId
             ]);
         } catch (\Exception $e) {
             return false;
         }
     }

     public function updateSoldeByCompteId(int $compteId, float $nouveauSolde): bool
     {
         try {
             $sql = "UPDATE $this->table SET solde = :solde WHERE id = :compte_id";
             $stmt = $this->pdo->prepare($sql);
             return $stmt->execute([
                 ':solde' => $nouveauSolde,
                 ':compte_id' => $compteId
             ]);
         } catch (\Exception $e) {
             error_log("Erreur updateSoldeByCompteId: " . $e->getMessage());
             return false;
         }
     }

     public function findById(int $compteId): ?array
     {
         try {
             $sql = "SELECT * FROM $this->table WHERE id = :compte_id";
             $stmt = $this->pdo->prepare($sql);
             $stmt->execute([':compte_id' => $compteId]);
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
             return $result ?: null;
         } catch (\Exception $e) {
             error_log("Erreur findById: " . $e->getMessage());
             return null;
         }
     }

     public function getComptesByUserId(int $userId): array
     {
         try {
             $sql = "SELECT id, user_id, num_compte, solde, type, num_telephone FROM $this->table WHERE user_id = :user_id ORDER BY type DESC";
             $stmt = $this->pdo->prepare($sql);
             $stmt->execute([':user_id' => $userId]);
             return $stmt->fetchAll(PDO::FETCH_ASSOC);
         } catch (\Exception $e) {
             return [];
         }
     }

     public function changeTypeCompte(int $compteId, string $nouveauType): bool
     {
         try {
             $sql = "UPDATE $this->table SET type = :type WHERE id = :id";
             $stmt = $this->pdo->prepare($sql);
             return $stmt->execute([
                 ':type' => $nouveauType,
                 ':id' => $compteId
             ]);
         } catch (\Exception $e) {
             return false;
         }
     }

     public function getCompteById(int $compteId): ?array
     {
         try {
             $sql = "SELECT * FROM $this->table WHERE id = :id";
             $stmt = $this->pdo->prepare($sql);
             $stmt->execute([':id' => $compteId]);
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
             return $result ?: null;
         } catch (\Exception $e) {
             return null;
         }
     }

     public function update(){}
     public function delete(){}
     public function selectById(){}
     public function selectBy(array $filter){}

}