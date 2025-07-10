<?php

namespace Srx\Service;

use Src\Repository\CompteRepository;
use Src\Repository\UserRepository;
use Srx\Entity\Compte;
use Srx\Entity\User;
use Srx\Entity\TypeUser;
use App\Config\Database;
use PDO;
use Exception;

class CompteService
{
    private CompteRepository $compteRepo;
    private UserRepository $userRepo;
    private PDO $pdo;

    public function __construct()
    {
        $this->compteRepo = CompteRepository::getInstance();
        $this->userRepo = UserRepository::getInstance();
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupérer les comptes d'un utilisateur
     */
    public function getComptesByUserId(int $userId): array
    {
        return $this->compteRepo->findByUserId($userId);
    }

    /**
     * Créer un utilisateur avec son compte principal
     * Cette méthode utilise une transaction pour garantir la cohérence
     */
    public function creerUtilisateurAvecComptePrincipal(array $userData): array
    {
        try {
            // Validation des données
            $this->validateUserData($userData);

            // Démarrer la transaction
            $this->pdo->beginTransaction();

            // 1. Créer le TypeUser (client par défaut)
            $typeUser = new TypeUser('client');

            // 2. Créer l'utilisateur
            $user = new User(
                $userData['nom'],
                $userData['prenom'],
                $typeUser,
                $userData['adresse'],
                $userData['numero_cni'],
                $userData['photo_recto'] ?? '',
                $userData['photo_verso'] ?? '',
                $userData['telephone'],
                $userData['password']
            );

            // 3. Sauvegarder l'utilisateur
            if (!$this->userRepo->create($user)) {
                throw new Exception("Erreur lors de la création de l'utilisateur");
            }

            // 4. Récupérer l'ID de l'utilisateur créé
            $userId = $this->userRepo->getLastInsertId();
            
            // 5. Récupérer l'utilisateur complet depuis la base
            $userFromDb = $this->userRepo->findById($userId);
            if (!$userFromDb) {
                throw new Exception("Erreur lors de la récupération de l'utilisateur créé");
            }

            // 6. Créer le compte principal
            $numCompte = $this->compteRepo->generateNumCompte();
            
            $comptePrincipal = new Compte(
                $numCompte,
                0.0, // Solde initial à 0
                'ComptePrincipal',
                $userFromDb
            );

            // Définir le numéro de téléphone du compte (même que l'utilisateur)
            $this->setCompteNumTelephone($comptePrincipal, $userData['telephone']);

            // 7. Sauvegarder le compte principal
            if (!$this->compteRepo->create($comptePrincipal)) {
                throw new Exception("Erreur lors de la création du compte principal");
            }

            // Valider la transaction
            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Utilisateur et compte principal créés avec succès',
                'data' => [
                    'user_id' => $userId,
                    'num_compte' => $numCompte,
                    'telephone' => $userData['telephone'],
                    'nom_complet' => $userData['nom'] . ' ' . $userData['prenom']
                ]
            ];

        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => $this->getErrorCode($e->getMessage())
            ];
        }
    }

    /**
     * Créer un compte secondaire pour un utilisateur existant
     */
    public function creerCompteSecondaire(int $userId): array
    {
        try {
            // Vérifier que l'utilisateur existe
            $user = $this->userRepo->findById($userId);
            if (!$user) {
                throw new Exception("Utilisateur introuvable");
            }

            // Vérifier que l'utilisateur n'a pas déjà trop de comptes
            $comptesExistants = $this->getComptesByUserId($userId);
            if (count($comptesExistants) >= 5) { // Limite arbitraire
                throw new Exception("Limite de comptes atteinte (maximum 5 comptes par utilisateur)");
            }

            // Générer un nouveau numéro de compte
            $numCompte = $this->compteRepo->generateNumCompte();

            // Créer le compte secondaire
            $compteSecondaire = new Compte(
                $numCompte,
                0.0,
                'CompteSecondaire',
                $user
            );

            // Définir le numéro de téléphone
            $this->setCompteNumTelephone($compteSecondaire, $user->getTelephone());

            // Sauvegarder
            if (!$this->compteRepo->create($compteSecondaire)) {
                throw new Exception("Erreur lors de la création du compte secondaire");
            }

            return [
                'success' => true,
                'message' => 'Compte secondaire créé avec succès',
                'data' => [
                    'num_compte' => $numCompte,
                    'type' => 'CompteSecondaire',
                    'solde' => 0.0
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Récupérer les informations complètes d'un utilisateur avec ses comptes
     */
    public function getUtilisateurAvecComptes(int $userId): array
    {
        try {
            $user = $this->userRepo->findById($userId);
            if (!$user) {
                throw new Exception("Utilisateur introuvable");
            }

            $comptes = $this->getComptesByUserId($userId);

            return [
                'success' => true,
                'data' => [
                    'utilisateur' => [
                        'id' => $user->getId(),
                        'nom' => $user->getNom(),
                        'prenom' => $user->getPrenom(),
                        'telephone' => $user->getTelephone(),
                        'type' => $user->getType()->getType()
                    ],
                    'comptes' => array_map(function($compte) {
                        return [
                            'id' => $compte->getId(),
                            'num_compte' => $compte->getNumCompte(),
                            'solde' => $compte->getSolde(),
                            'type' => $compte->getType()
                        ];
                    }, $comptes),
                    'nombre_comptes' => count($comptes)
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier si un utilisateur peut créer un nouveau compte
     */
    public function peutCreerNouveauCompte(int $userId): bool
    {
        $comptes = $this->getComptesByUserId($userId);
        return count($comptes) < 5; // Limite arbitraire
    }

    /**
     * Rechercher un compte par son numéro
     */
    public function rechercherCompteParNumero(string $numCompte): array
    {
        try {
            // Cette méthode devrait être ajoutée au CompteRepository
            // Pour l'instant, on simule
            
            return [
                'success' => true,
                'data' => [
                    'existe' => $this->compteRepo->existsByNumCompte($numCompte)
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Valider les données utilisateur
     */
    private function validateUserData(array $userData): void
    {
        $requiredFields = ['nom', 'prenom', 'adresse', 'numero_cni', 'telephone', 'password'];
        
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                throw new Exception("Le champ {$field} est requis");
            }
        }

        // Validation du format du téléphone (exemple pour le Sénégal)
        if (!$this->isValidTelephone($userData['telephone'])) {
            throw new Exception("Format de téléphone invalide");
        }

        // Validation du mot de passe
        if (strlen($userData['password']) < 6) {
            throw new Exception("Le mot de passe doit contenir au moins 6 caractères");
        }

        // Vérifier si le téléphone existe déjà
        if ($this->userRepo->existsByTelephone($userData['telephone'])) {
            throw new Exception("Ce numéro de téléphone est déjà utilisé");
        }

        // Vérifier si le numéro CNI existe déjà
        if ($this->userRepo->existsByNumCarteIdentite($userData['numero_cni'])) {
            throw new Exception("Ce numéro de CNI est déjà utilisé");
        }
    }

    /**
     * Valider le format du téléphone
     */
    private function isValidTelephone(string $telephone): bool
    {
        // Exemple de validation pour les numéros sénégalais
        // Ajustez selon vos besoins
        return preg_match('/^(77|78|70|76|75)[0-9]{7}$/', $telephone);
    }

    /**
     * Définir le numéro de téléphone d'un compte via réflexion
     */
    private function setCompteNumTelephone(Compte $compte, string $telephone): void
    {
        $reflection = new \ReflectionClass($compte);
        $numTelProperty = $reflection->getProperty('num_telephone');
        $numTelProperty->setAccessible(true);
        $numTelProperty->setValue($compte, (int)$telephone);
    }

    /**
     * Obtenir un code d'erreur basé sur le message
     */
    private function getErrorCode(string $message): string
    {
        if (strpos($message, 'téléphone') !== false) {
            return 'TELEPHONE_EXISTE';
        }
        if (strpos($message, 'CNI') !== false) {
            return 'CNI_EXISTE';
        }
        if (strpos($message, 'mot de passe') !== false) {
            return 'PASSWORD_INVALID';
        }
        if (strpos($message, 'téléphone invalide') !== false) {
            return 'TELEPHONE_FORMAT_INVALID';
        }
        
        return 'ERREUR_GENERALE';
    }

    /**
     * Obtenir les statistiques des comptes d'un utilisateur
     */
    public function getStatistiquesComptes(int $userId): array
    {
        try {
            $comptes = $this->getComptesByUserId($userId);
            
            $stats = [
                'nombre_comptes' => count($comptes),
                'solde_total' => 0,
                'compte_principal' => null,
                'comptes_secondaires' => []
            ];

            foreach ($comptes as $compte) {
                $stats['solde_total'] += $compte->getSolde();
                
                if ($compte->getType() === 'ComptePrincipal') {
                    $stats['compte_principal'] = [
                        'num_compte' => $compte->getNumCompte(),
                        'solde' => $compte->getSolde()
                    ];
                } else {
                    $stats['comptes_secondaires'][] = [
                        'num_compte' => $compte->getNumCompte(),
                        'solde' => $compte->getSolde()
                    ];
                }
            }

            return [
                'success' => true,
                'data' => $stats
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    /**
 * Trouver un compte par son numéro
 */
public function findByNumCompte(string $numCompte): ?Compte
{
    $sql = "SELECT c.*, u.telephone 
            FROM compte c 
            LEFT JOIN user u ON c.user_id = u.id 
            WHERE c.numCompte = :numCompte";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':numCompte' => $numCompte]);
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        return null;
    }
    
    return Compte::toObject([
        'id' => $data['id'],
        'num_compte' => $data['numCompte'],
        'num_telephone' => $data['num_telephone'],
        'solde' => $data['solde'],
        'type' => $data['type'],
        'user_id' => $data['user_id'],
        'transactions' => []
    ]);
}

}
