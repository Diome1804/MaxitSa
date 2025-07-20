<?php
namespace Src\Service;

use Src\Repository\CompteRepository;
use App\Core\App;

class CompteService
{
    private CompteRepository $compteRepository;

    private static ?CompteService $instance = null;

    public static function getInstance(): CompteService
    {
        if (self::$instance === null) {
            self::$instance = new CompteService();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->compteRepository = App::getDependency('repository', 'compteRepo');
    }

   

    public function getSolde(int $userId): float
    {
        return $this->compteRepository->getsoldeByUserId($userId);
    }

    public function createCompteSecondaire(int $userId, string $numero, float $soldeInitial): array
    {
        $soldePrincipal = $this->compteRepository->getSoldeByUserId($userId);
        
        if ($soldePrincipal < $soldeInitial) {
            return [
                'success' => false,
                'message' => 'Solde insuffisant dans le compte principal'
            ];
        }

        $nouveauSoldePrincipal = $soldePrincipal - $soldeInitial;
        
        $updateSuccess = $this->compteRepository->updateSolde($userId, $nouveauSoldePrincipal);
        if (!$updateSuccess) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du compte principal'
            ];
        }

        $numeroCompte = 'CPS' . time() . rand(1000, 9999);
        
        $insertSuccess = $this->compteRepository->insert([
            'user_id' => $userId,
            'num_compte' => $numeroCompte,
            'solde' => $soldeInitial,
            'type' => 'CompteSecondaire',
            'num_telephone' => $numero
        ]);

        if ($insertSuccess) {
            return [
                'success' => true,
                'message' => 'Compte secondaire créé avec succès'
            ];
        } else {
            $this->compteRepository->updateSolde($userId, $soldePrincipal);
            return [
                'success' => false,
                'message' => 'Erreur lors de la création du compte secondaire'
            ];
        }
    }

    public function getComptesByUserId(int $userId): array
    {
        return $this->compteRepository->getComptesByUserId($userId);
    }

    public function changerComptePrincipal(int $userId, int $nouveauComptePrincipalId): array
    {
        // Vérifier que le compte appartient à l'utilisateur
        $nouveauCompte = $this->compteRepository->getCompteById($nouveauComptePrincipalId);
        if (!$nouveauCompte || $nouveauCompte['user_id'] != $userId) {
            return [
                'success' => false,
                'message' => 'Compte non trouvé ou non autorisé'
            ];
        }

        // Vérifier que c'est bien un compte secondaire
        if ($nouveauCompte['type'] === 'ComptePrincipal') {
            return [
                'success' => false,
                'message' => 'Ce compte est déjà le compte principal'
            ];
        }

        // Récupérer l'ancien compte principal
        $comptes = $this->compteRepository->getComptesByUserId($userId);
        $ancienComptePrincipalId = null;
        
        foreach ($comptes as $compte) {
            if ($compte['type'] === 'ComptePrincipal') {
                $ancienComptePrincipalId = $compte['id'];
                break;
            }
        }

        if (!$ancienComptePrincipalId) {
            return [
                'success' => false,
                'message' => 'Aucun compte principal trouvé'
            ];
        }

        // Échanger les types
        $changeAncien = $this->compteRepository->changeTypeCompte($ancienComptePrincipalId, 'CompteSecondaire');
        $changeNouveau = $this->compteRepository->changeTypeCompte($nouveauComptePrincipalId, 'ComptePrincipal');

        if ($changeAncien && $changeNouveau) {
            return [
                'success' => true,
                'message' => 'Compte principal changé avec succès'
            ];
        } else {
            // Rollback en cas d'erreur
            $this->compteRepository->changeTypeCompte($ancienComptePrincipalId, 'ComptePrincipal');
            $this->compteRepository->changeTypeCompte($nouveauComptePrincipalId, 'CompteSecondaire');
            return [
                'success' => false,
                'message' => 'Erreur lors du changement de compte principal'
            ];
        }
    }

 
}



