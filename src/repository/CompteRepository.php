<?php

namespace App\Repository;

use App\Config\Database;
use App\Entity\Compte;
use App\Core\Abstract\AbstractRepository;
use App\Entity\User;
use PDO;

class CompteRepository extends AbstractRepository 
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getTableName(): string {
         return 'compte';
    }

    











    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM compte WHERE user_id = :userId");
        $stmt->execute(['userId' => $userId]);

        $userRepo = new UserRepository();
        $user = $userRepo->findById($userId);

        $comptes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $compte = new Compte(
                $row['num_compte'],
                (float)$row['solde'],
                $row['type'],
                $user
            );
            $comptes[] = $compte;
        }

        return $comptes;
    }
}
