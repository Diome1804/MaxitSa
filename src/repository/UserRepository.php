<?php

namespace Src\Repository;

use App\Config\Database;
use Src\Entity\User;
use PDO;

class UserRepository extends AbstractRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // public function findAll(): array
    // {
    //     $stmt = $this->db->query("SELECT * FROM \"user\"");
    //     $users = [];

    //     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //         $typeRepo = new TypeUserRepository();
    //         $type = $typeRepo->findById($row['type_id']);

    //         $user = new User($row['nom'], $row['prenom'], $type);
    //         // hydrate plus d’infos si besoin
    //         $users[] = $user;
    //     }

    //     return $users;
    // }

    // public function findById(int $id): ?User
    // {
    //     $stmt = $this->db->prepare("SELECT * FROM \"user\" WHERE id = :id");
    //     $stmt->execute(['id' => $id]);

    //     $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //     if ($row) {
    //         $typeRepo = new TypeUserRepository();
    //         $type = $typeRepo->findById($row['type_id']);

    //         return new User($row['nom'], $row['prenom'], $type);
    //     }

    //     return null;
    // }
}
