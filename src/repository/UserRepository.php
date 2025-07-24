<?php

namespace Src\Repository;

use Src\Entity\User;
use App\Core\Abstract\AbstractRepository;
use Src\Entity\TypeUser;
use App\Core\Interfaces\RepositoryInterface;
use App\Core\ReflectionFactory;
use PDO;

class UserRepository extends AbstractRepository implements RepositoryInterface
{
    private string $table = '"user"';

    public function __construct()
    {
        parent::__construct();
        $this->table = '"user"';
    }

    public function insert(array $userData): int|false
    {
        try {
            $sql = "INSERT INTO {$this->table} (nom, prenom, adresse, num_carte_identite, photorecto, photoverso, telephone, password, type_id) 
                    VALUES (:nom, :prenom, :adresse, :num_carte_identite, :photorecto, :photoverso, :telephone, :password, :type_id)";

            $stmt = $this->pdo->prepare($sql); // Utiliser $this->pdo
            $result = $stmt->execute([
                ':nom' => $userData['nom'],
                ':prenom' => $userData['prenom'],
                ':adresse' => $userData['adresse'],
                ':num_carte_identite' => $userData['num_carte_identite'],
                ':photorecto' => $userData['photorecto'],
                ':photoverso' => $userData['photoverso'],
                ':telephone' => $userData['telephone'],
                ':password' => $userData['password'],
                ':type_id' => $userData['type_id']
            ]);

            if ($result) {
                return $this->pdo->lastInsertId(); // Utiliser $this->pdo
            }
            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function findByTelephone(string $telephone): ?User
    {


        try {
            $sql = "SELECT u.*, t.type as type_name 
                FROM \"user\" u 
                JOIN typeuser t ON u.type_id = t.id 
                WHERE u.telephone = :telephone";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->execute();

            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                $factory = ReflectionFactory::getInstance();
                
                // Créer TypeUser avec réflexion
                $typeUser = $factory->create(TypeUser::class, [$userData['type_name']]);

                // Créer User avec réflexion
                $user = $factory->create(User::class, [
                    $userData['nom'],
                    $userData['prenom'],
                    $typeUser,
                    $userData['adresse'],
                    $userData['num_carte_identite'],
                    $userData['photorecto'],
                    $userData['photoverso'],
                    $userData['telephone'],
                    $userData['password']
                ]);

                // Définir l'ID
                if (isset($userData['id'])) {
                    $reflection = new \ReflectionClass($user);
                    $idProperty = $reflection->getProperty('id');
                    $idProperty->setAccessible(true);
                    $idProperty->setValue($user, $userData['id']);
                }

                return $user;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    
    /**
     * Trouver un utilisateur par ID
     */
    public function findById(int $id): array|false
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->pdo->prepare($sql); // Utiliser $this->pdo
            $stmt->execute([':id' => $id]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: false;

        } catch (\Exception $e) {
            error_log("Erreur findById UserRepository: " . $e->getMessage());
            return false;
        }
    }

    // Méthodes abstraites obligatoires
    public function selectAll()
    {
        // Implémentation si nécessaire
    }

    public function update()
    {
        // Implémentation si nécessaire
    }

    public function delete()
    {
        // Implémentation si nécessaire
    }

    public function selectById()
    {
        // Implémentation si nécessaire
    }

    public function selectBy(array $filter)
    {
        // Implémentation si nécessaire
    }
}