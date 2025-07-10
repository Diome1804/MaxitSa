<?php

namespace Src\Repository;

use App\Config\Database;
use Src\Entity\User;
use Src\Entity\TypeUser;
use App\Core\Abstract\AbstractRepository;
use PDO;

class UserRepository extends AbstractRepository
{
    protected \PDO $pdo;
    private static ?UserRepository $instance = null;

    public function __construct()
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

    private function __clone() {}

    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }

    public function getTableName(): string 
    {
        return 'user';
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function create(User $user): bool
    {
        $sql = "INSERT INTO user (nom, prenom, adresse, numCarteIdentite, photorecto, photoverso, telephone, password, type_id) 
                VALUES (:nom, :prenom, :adresse, :numCarteIdentite, :photorecto, :photoverso, :telephone, :password, :type_id)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nom' => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':adresse' => $user->getAdresse(),
            ':numCarteIdentite' => $user->getNumCarteIdentite(),
            ':photorecto' => $user->getPhotorecto(),
            ':photoverso' => $user->getPhotoverso(),
            ':telephone' => $user->getTelephone(),
            ':password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
            ':type_id' => $user->getType()->getId()
        ]);
    }

    /**
     * Vérifier si un numéro de téléphone existe déjà
     */
    public function existsByTelephone(string $telephone): bool
    {
        $sql = "SELECT COUNT(*) FROM user WHERE telephone = :telephone";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':telephone' => $telephone]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifier si un numéro de CNI existe déjà
     */
    public function existsByNumCarteIdentite(string $numCarteIdentite): bool
    {
        $sql = "SELECT COUNT(*) FROM user WHERE numCarteIdentite = :numCarteIdentite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':numCarteIdentite' => $numCarteIdentite]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Récupérer le dernier ID inséré
     */
    public function getLastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Trouver un utilisateur par son téléphone
     */
    public function findByTelephone(string $telephone): ?User
    {
        $sql = "SELECT u.*, t.type as type_name 
                FROM user u 
                LEFT JOIN type_user t ON u.type_id = t.id 
                WHERE u.telephone = :telephone";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':telephone' => $telephone]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        // Utilisation de votre méthode toObject
        return User::toObject([
            'id' => $data['id'],
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'adresse' => $data['adresse'],
            'numCarteIdentite' => $data['numCarteIdentite'],
            'photorecto' => $data['photorecto'],
            'photoverso' => $data['photoverso'],
            'telephone' => $data['telephone'],
            'password' => $data['password'],
            'type' => $data['type_name'] ?? 'client',
            'comptes' => []
        ]);
    }

    /**
     * Trouver un utilisateur par son ID
     */
    public function findById(int $id): ?User
    {
        $sql = "SELECT u.*, t.type as type_name 
                FROM user u 
                LEFT JOIN type_user t ON u.type_id = t.id 
                WHERE u.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        // Utilisation de votre méthode toObject
        return User::toObject([
            'id' => $data['id'],
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'adresse' => $data['adresse'],
            'numCarteIdentite' => $data['numCarteIdentite'],
            'photorecto' => $data['photorecto'],
            'photoverso' => $data['photoverso'],
            'telephone' => $data['telephone'],
            'password' => $data['password'],
            'type' => $data['type_name'] ?? 'client',
            'comptes' => []
        ]);
    }
}
