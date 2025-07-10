<?php

namespace Src\Repository;

use App\Config\Database;
use Src\Entity\Compte;
use Src\Entity\User;
use App\Core\Abstract\AbstractRepository;
use PDO;

class CompteRepository extends AbstractRepository
{
    protected \PDO $pdo;
    private static ?CompteRepository $instance = null;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public static function getInstance(): CompteRepository
    {
        if (self::$instance === null) {
            self::$instance = new CompteRepository();
        }
        return self::$instance;
    }

    public function getTableName(): string 
    {
        return 'compte';
    }

    /**
     * Créer un nouveau compte
     */
    public function create(Compte $compte): bool
    {
        $sql = "INSERT INTO compte (numCompte, num_telephone, solde, type, user_id) 
                VALUES (:numCompte, :num_telephone, :solde, :type, :user_id)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':numCompte' => $compte->getNumCompte(),
            ':num_telephone' => $compte->getNumTelephone(),
            ':solde' => $compte->getSolde(),
            ':type' => $compte->getType(),
            ':user_id' => $compte->getUser()->getId()
        ]);
    }

    /**
     * Générer un numéro de compte unique
     */
    public function generateNumCompte(): string
    {
        do {
            $numCompte = 'CPT' . date('Y') . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while ($this->existsByNumCompte($numCompte));
        
        return $numCompte;
    }

    /**
     * Vérifier si un numéro de compte existe
     */
    public function existsByNumCompte(string $numCompte): bool
    {
        $sql = "SELECT COUNT(*) FROM compte WHERE numCompte = :numCompte";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':numCompte' => $numCompte]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Trouver les comptes d'un utilisateur
     */
    public function findByUserId(int $userId): array
    {
        $sql = "SELECT c.*, u.telephone 
                FROM compte c 
                LEFT JOIN user u ON c.user_id = u.id 
                WHERE c.user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        $comptes = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Utilisation de votre méthode toObject
            $comptes[] = Compte::toObject([
                'id' => $data['id'],
                'num_compte' => $data['numCompte'],
                'num_telephone' => $data['num_telephone'],
                'solde' => $data['solde'],
                'type' => $data['type'],
                'user_id' => $data['user_id'],
                'transactions' => []
            ]);
        }
        
        return $comptes;
    }
}
