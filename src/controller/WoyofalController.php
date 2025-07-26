<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use App\Core\Interfaces\WoyofalServiceInterface;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Lang;

class WoyofalController extends AbstractController
{
    private WoyofalServiceInterface $woyofalService;
    private Validator $validator;

    public function __construct(Session $session, WoyofalServiceInterface $woyofalService, Validator $validator)
    {
        parent::__construct($session);
        $this->woyofalService = $woyofalService;
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
        
        // Récupérer l'historique des achats
        $historique = $this->woyofalService->getHistoriqueAchats($user['id'], 5);

        $this->render('woyofal/index.html.php', [
            'user' => $user,
            'historique' => $historique,
            'success' => Session::get('success'),
            'error' => Session::get('error'),
            'errors' => Session::get('errors'),
            'old' => Session::get('old'),
            'show_receipt_popup' => Session::get('show_receipt_popup'),
            'recu_data' => Session::get('recu_data'),
            'transaction_id' => Session::get('transaction_id')
        ]);

        // Nettoyer les messages de session
        Session::unset('success');
        Session::unset('error');
        Session::unset('errors');
        Session::unset('old');
        Session::unset('show_receipt_popup');
        Session::unset('recu_data');
        Session::unset('transaction_id');
    }

    public function acheter()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/woyofal');
            exit();
        }

        $user = Session::get('user');
        $compteur = trim($_POST['compteur'] ?? '');
        $montant = (float)($_POST['montant'] ?? 0);

        // Sauvegarder les données saisies pour les réafficher en cas d'erreur
        Session::set('old', [
            'compteur' => $compteur,
            'montant' => $montant
        ]);

        // Validation côté serveur
        $erreurs = $this->validerFormulaire($compteur, $montant);
        if (!empty($erreurs)) {
            Session::set('errors', $erreurs);
            $this->redirect(APP_URL . '/woyofal');
            exit();
        }

        // Appel du service Woyofal
        $resultat = $this->woyofalService->acheterCode($compteur, $montant, $user['id']);

        if ($resultat['success']) {
            Session::set('success', $resultat['message']);
            Session::set('show_receipt_popup', true);
            Session::set('recu_data', $resultat['recu']);
            Session::set('transaction_id', $resultat['transaction_id']);
            Session::unset('old');
            
            // Rediriger vers la page principale Woyofal avec popup
            $this->redirect(APP_URL . '/woyofal');
        } else {
            Session::set('error', $resultat['message']);
            if (isset($resultat['errors'])) {
                Session::set('errors', $resultat['errors']);
            }
            $this->redirect(APP_URL . '/woyofal');
        }
    }

    public function recu()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        $user = Session::get('user');
        
        // Essayer d'abord de récupérer depuis la session (pour un achat récent)
        $recuData = Session::get('recu_data');
        $transactionId = Session::get('transaction_id');
        
        // Si pas en session, vérifier si un ID de transaction est passé en paramètre
        if (!$recuData && isset($_GET['id'])) {
            $transactionId = (int)$_GET['id'];
            $recuData = $this->woyofalService->getRecuParTransaction($transactionId, $user['id']);
        }

        if (!$recuData || !$transactionId) {
            Session::set('error', 'Reçu introuvable');
            $this->redirect(APP_URL . '/woyofal');
            exit();
        }

        $this->render('woyofal/recu.html.php', [
            'user' => $user,
            'recu' => $recuData,
            'transaction_id' => $transactionId
        ]);

        // Ne nettoyer la session que si c'est un achat récent
        if (Session::isset('recu_data')) {
            Session::unset('recu_data');
            Session::unset('transaction_id');
        }
    }

    public function historique()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        $user = Session::get('user');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        // Récupérer l'historique complet avec pagination
        $historique = $this->woyofalService->getHistoriqueAchats($user['id'], $perPage * $page);

        $this->render('woyofal/historique.html.php', [
            'user' => $user,
            'historique' => $historique,
            'page' => $page,
            'perPage' => $perPage
        ]);
    }

    public function create()
    {
        // Implémentation si nécessaire
        $this->redirect(APP_URL . '/woyofal');
    }

    public function store()
    {
        // Alias pour acheter()
        $this->acheter();
    }

    public function edit()
    {
        // Non applicable pour Woyofal
        $this->redirect(APP_URL . '/woyofal');
    }

    public function show()
    {
        // Alias pour recu()
        $this->recu();
    }

    private function validerFormulaire(string $compteur, float $montant): array
    {
        $erreurs = [];

        // Validation du compteur
        if (empty($compteur)) {
            $erreurs['compteur'] = Lang::get('woyofal.compteur_required');
        } elseif (!preg_match('/^[0-9]{9,11}$/', $compteur)) {
            $erreurs['compteur'] = Lang::get('woyofal.compteur_invalid');
        }

        // Validation du montant
        if ($montant <= 0) {
            $erreurs['montant'] = Lang::get('woyofal.montant_required');
        } elseif ($montant < 500) {
            $erreurs['montant'] = Lang::get('woyofal.montant_minimum');
        } elseif ($montant > 500000) {
            $erreurs['montant'] = Lang::get('woyofal.montant_maximum');
        }

        return $erreurs;
    }

    private function redirect(string $url): void
    {
        header("Location: $url");
        exit();
    }
}
