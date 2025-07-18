<?php

namespace Src\Repository;
use App\Core\Abstract\AbstractRepository;
use PDO;

class CompteRepository extends AbstractRepository{

    private string $table = 'compte';

    private static CompteRepository|null $instance = null;

    public static function getInstance():CompteRepository{
        if(self::$instance == null){
            self::$instance = new CompteRepository();
        }
        return self::$instance;
    }

    public function __construct(){
        parent::__construct();
    }

     public function selectAll(){}

    
     // Méthode pour récupérer le solde principal d'un utilisateur par son ID
    public function getSoldeByUserId(int $userId): float
    {
        try {
            $sql = "SELECT solde FROM $this->table WHERE user_id = :user_id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (float) $result['solde'] : 0.0;
        } catch (\Exception $e) {
                return 0.0;
        }
    }



     public function insert(array $data){}
     public function update(){}
     public function delete(){}
     public function selectById(){}
     public function selectBy(array $filter){}

}