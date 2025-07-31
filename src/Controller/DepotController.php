<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use App\Core\Interfaces\DepotServiceInterface;
use App\Core\Interfaces\CompteServiceInterface;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Lang;

class DepotController extends AbstractController
{
    private DepotServiceInterface $depotService;
    private CompteServiceInterface $compteService;
    private Validator $validator;

    public function __construct(
        Session $session, 
        DepotServiceInterface $depotService,
        CompteServiceInterface $compteService,
        Validator $validator
    ) {
        parent::__construct($session);
        $this->depotService = $depotService;
        $this->compteService = $compteService;
        $this->validator = $validator;
        Lang::detectLang();
    }

    public function index()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        $user = Session::get('user');
        
        // Récupérer les comptes de l'utilisateur
        $comptes = $this->compteService->getComptesUtilisateur($user['id']);
        
        // Récupérer les transactions annulables
        $transactionsAnnulables = $this->depotService->getTransactionsAnnulables($user['id']);

        $this->render('depot/index.html.php', [
            'user' => $user,
            'comptes' => $comptes,
            'transactions_annulables' => $transactionsAnnulables,
            'success' => Session::get('success'),
            'error' => Session::get('error'),
            'errors' => Session::get('errors'),
            'old' => Session::get('old')
        ]);

        // Nettoyer les messages de session
        Session::unset('success');
        Session::unset('error');
        Session::unset('errors');
        Session::unset('old');
    }

    public function depot()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/depot');
            exit();
        }

        $user = Session::get('user');
        $compteId = (int)($_POST['compte_id'] ?? 0);
        $montant = (float)($_POST['montant'] ?? 0);

        // Sauvegarder les données saisies
        Session::set('old', [
            'compte_id' => $compteId,
            'montant' => $montant
        ]);

        // Validation
        $erreurs = $this->validerDepot($compteId, $montant);
        if (!empty($erreurs)) {
            Session::set('errors', $erreurs);
            $this->redirect(APP_URL . '/depot');
            exit();
        }

        // Effectuer le dépôt
        $resultat = $this->depotService->effectuerDepot($compteId, $montant, $user['id']);

        if ($resultat['success']) {
            Session::set('success', $resultat['message']);
            Session::unset('old');
        } else {
            Session::set('error', $resultat['message']);
            if (isset($resultat['errors'])) {
                Session::set('errors', $resultat['errors']);
            }
        }

        $this->redirect(APP_URL . '/depot');
    }

    public function transfert()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/depot');
            exit();
        }

        $user = Session::get('user');
        $compteSourceId = (int)($_POST['compte_source_id'] ?? 0);
        $compteDestinationId = (int)($_POST['compte_destination_id'] ?? 0);
        $montant = (float)($_POST['montant_transfert'] ?? 0);

        // Sauvegarder les données saisies
        Session::set('old', [
            'compte_source_id' => $compteSourceId,
            'compte_destination_id' => $compteDestinationId,
            'montant_transfert' => $montant
        ]);

        // Validation
        $erreurs = $this->validerTransfert($compteSourceId, $compteDestinationId, $montant);
        if (!empty($erreurs)) {
            Session::set('errors', $erreurs);
            $this->redirect(APP_URL . '/depot');
            exit();
        }

        // Effectuer le transfert
        $resultat = $this->depotService->effectuerTransfert($compteSourceId, $compteDestinationId, $montant, $user['id']);

        if ($resultat['success']) {
            Session::set('success', $resultat['message']);
            Session::unset('old');
        } else {
            Session::set('error', $resultat['message']);
            if (isset($resultat['errors'])) {
                Session::set('errors', $resultat['errors']);
            }
        }

        $this->redirect(APP_URL . '/depot');
    }

    public function annuler()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/depot');
            exit();
        }

        $user = Session::get('user');
        $transactionId = (int)($_POST['transaction_id'] ?? 0);

        if ($transactionId <= 0) {
            Session::set('error', 'Transaction invalide');
            $this->redirect(APP_URL . '/depot');
            exit();
        }

        // Effectuer l'annulation
        $resultat = $this->depotService->annulerDepot($transactionId, $user['id']);

        if ($resultat['success']) {
            Session::set('success', $resultat['message']);
        } else {
            Session::set('error', $resultat['message']);
        }

        $this->redirect(APP_URL . '/depot');
    }

    public function calculerFrais()
    {
        if (!Session::isset('user')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Non autorisé']);
            exit();
        }

        $compteSourceId = (int)($_GET['source'] ?? 0);
        $compteDestinationId = (int)($_GET['destination'] ?? 0);
        $montant = (float)($_GET['montant'] ?? 0);

        if ($compteSourceId && $compteDestinationId && $montant > 0) {
            // Récupérer les vrais types de comptes depuis la base de données
            $comptes = $this->compteService->getComptesUtilisateur(Session::get('user')['id']);
            
            $typeSource = null;
            $typeDestination = null;
            
            foreach ($comptes as $compte) {
                if ($compte['id'] == $compteSourceId) {
                    $typeSource = $compte['type'];
                }
                if ($compte['id'] == $compteDestinationId) {
                    $typeDestination = $compte['type'];
                }
            }
            
            // Si on ne trouve pas les types, définir par défaut comme ComptePrincipal
            if (!$typeSource) {
                $typeSource = 'ComptePrincipal';
            }
            if (!$typeDestination) {
                $typeDestination = 'ComptePrincipal';
            }
            
            $frais = $this->depotService->calculerFraisTransfert($typeSource, $typeDestination, $montant);
            
            header('Content-Type: application/json');
            echo json_encode([
                'frais' => $frais,
                'montant_total' => $montant + $frais,
                'frais_formate' => number_format($frais, 0, ',', ' ') . ' FCFA'
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Paramètres invalides']);
        }
    }

    // Méthodes abstraites
    public function create() {
        $this->redirect(APP_URL . '/depot');
    }

    public function store() {
        $this->depot();
    }

    public function edit() {
        $this->redirect(APP_URL . '/depot');
    }

    public function show() {
        $this->index();
    }

    private function validerDepot(int $compteId, float $montant): array
    {
        $erreurs = [];

        if ($compteId <= 0) {
            $erreurs['compte_id'] = 'Veuillez sélectionner un compte';
        }

        if ($montant <= 0) {
            $erreurs['montant'] = 'Le montant doit être supérieur à 0';
        } elseif ($montant > 10000000) { // 10 millions max
            $erreurs['montant'] = 'Le montant ne peut pas dépasser 10 000 000 FCFA';
        }

        return $erreurs;
    }

    private function validerTransfert(int $compteSourceId, int $compteDestinationId, float $montant): array
    {
        $erreurs = [];

        if ($compteSourceId <= 0) {
            $erreurs['compte_source_id'] = 'Veuillez sélectionner le compte source';
        }

        if ($compteDestinationId <= 0) {
            $erreurs['compte_destination_id'] = 'Veuillez sélectionner le compte destination';
        }

        if ($compteSourceId === $compteDestinationId) {
            $erreurs['compte_destination_id'] = 'Le compte source et destination doivent être différents';
        }

        if ($montant <= 0) {
            $erreurs['montant_transfert'] = 'Le montant doit être supérieur à 0';
        } elseif ($montant > 10000000) {
            $erreurs['montant_transfert'] = 'Le montant ne peut pas dépasser 10 000 000 FCFA';
        }

        return $erreurs;
    }

    private function redirect(string $url): void
    {
        header("Location: $url");
        exit();
    }
}
