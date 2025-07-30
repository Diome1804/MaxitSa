<?php
namespace Src\Service;

use Src\Repository\CompteRepository;
use Src\Repository\UserRepository;
use App\Core\Validator;
use Src\Entity\User;
use App\Core\Database;
use App\Core\MiddlewareLoader;
use App\Core\Interfaces\SecurityServiceInterface;
use App\Core\ReflectionFactory;

class SecurityService implements SecurityServiceInterface
{
    private CompteRepository $compteRepository;
    private UserRepository $userRepository;
    private Database $database;
    private ReflectionFactory $factory;

    public function __construct(CompteRepository $compteRepository, UserRepository $userRepository, Database $database, ReflectionFactory $factory){
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository;
        $this->database = $database;
        $this->factory = $factory;
    }

    /**
     * Créer un client avec un compte principal
     */
    public function creerClientAvecComptePrincipal(array $userData, float $soldeInitial = 0.0): int|false
    {
        try {
            // Récupérer la connexion PDO via Database
            $pdo = $this->database->getConnection(); // Récupérer la connexion PDO
            
            $pdo->beginTransaction();

            // Créer le client
            $userData['password'] = MiddlewareLoader::execute('crypt', $userData['password']);
            $userData['type_id'] = 1; // Client (type_user_id dans la base)
            
            $userId = $this->userRepository->insert($userData);
            if (!$userId) {
                $pdo->rollBack();
                return false;
            }

            // Créer le compte principal
            $compteData = [
                'num_compte' => 'CPT' . time() . rand(1000, 9999),
                'solde' => $soldeInitial,
                'user_id' => $userId,
                'type' => 'ComptePrincipal',
                'num_telephone' => $userData['telephone']
            ];

            if (!$this->compteRepository->insert($compteData)) {
                $pdo->rollBack();
                return false;
            }

            $pdo->commit();
            return $userId;

        } catch (\Exception $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            error_log("Erreur dans creerClientAvecComptePrincipal: " . $e->getMessage());
            return false;
        }
    }

    public function authenticate(array $data): ?User
    {
    // error_log("=== AUTHENTICATE ===");
    // error_log("Données reçues: " . print_r($data, true));
    
    // Vérification simple des champs requis
    if (empty($data['numero']) || empty($data['password'])) {
    return null;
    }

    // Connexion directe
    $user = $this->login(trim($data['numero']), trim($data['password']));
    
    return $user;
}

public function login(string $numero, string $password): ?User
{
    // error_log("=== LOGIN ===");
    // error_log("Numéro: '$numero'");
    // error_log("Mot de passe fourni: " . (empty($password) ? 'VIDE' : 'OK'));
    
    try {
        // Recherche par numéro (téléphone)
        $user = $this->userRepository->findByTelephone($numero);
        
        if ($user) {
            error_log("Utilisateur trouvé: " . $user->getNom());
            error_log("Hash en base: " . $user->getPassword());
            
            // Vérification du mot de passe - utiliser la même méthode que pour le cryptage
            if (password_verify($password, $user->getPassword())) {
                // error_log("✅ Connexion réussie");
                return $user;
            } else {
                //error_log("❌ Mot de passe incorrect");
            }
        } else {
            //error_log("❌ Aucun utilisateur trouvé avec ce numéro");
        }
    } catch (\Exception $e) {
        error_log("Erreur dans login: " . $e->getMessage());
    }
    
    return null;
}

}