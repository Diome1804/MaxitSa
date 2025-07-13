<?php

namespace Src\Repository;
use App\Core\Abstract\AbstractRepository;

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
   

    public function insert(array $data): bool {
    $query = "INSERT INTO compte (num_compte,solde,user_id,type,num_telephone) 
              VALUES (:num_compte, :solde, :user_id, :type, :num_telephone)";
    $stmt = $this->pdo->prepare($query);
    return $stmt->execute($data);
    }

// public function findPrincipalByUserId($userId): ?array {
//     $query = "SELECT * FROM compte WHERE id_user = :id_user AND typeCompte = 'Principal'";
//     $stmt = $this->pdo->prepare($query);
//     $stmt->execute(['id_user' => $userId]);
//     return $stmt->fetch() ?: null;
// }
// public function selectByClient($user_id){
//         $sql = "SELECT * from $this->table where id_user = :user_id";
//         $stmt = $this->pdo->prepare($sql);
//         $result = $stmt->execute(['user_id' => $user_id]);
//         if($result){
//             return $stmt->fetchAll();
//         }
//         return null;
// }


     public function update(){}
     public function delete(){}
     public function selectById(){}
     public function selectBy(array $filter){}

}