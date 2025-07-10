<?php

namespace Src\Repository;

use App\Config\Database;
use Src\Entity\User;
use App\Core\Abstract\AbstractRepository;
use PDO;

class UserRepository extends AbstractRepository
{
    protected \PDO $pdo;
    private static ?UserRepository $instance = null;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public static function getInstance(): UserRepository{
        if (self::$instance === null) {
            self::$instance = new UserRepository();
        }
        return self::$instance;
    }

    // Empêcher le clonage
    private function __clone() {}

    // Empêcher la désérialisation
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }

    public function getTableName():string {
        return  'user';
    }






}
