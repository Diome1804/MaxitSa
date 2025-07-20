<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\CompteService;
use Src\Service\TransactionService;
use App\Core\App;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Lang;

class CompteController extends AbstractController
{
    private TransactionService $transactionService;
    private CompteService $compteService;

    public function __construct()
    {
        parent::__construct();
        $this->transactionService = App::getDependency('services', 'transactionServ');
        $this->compteService = App::getDependency('services', 'compteServ');
        
        // Détecter et configurer la langue
        Lang::detectLang();
    }

    public function index()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        $user = Session::get('user');

        $solde = $this->compteService->getSolde($user['id']);
        $comptes = $this->compteService->getComptesByUserId($user['id']);

        // Pagination pour les transactions
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 5; // 5 transactions par page pour bien voir la pagination
        $result = $this->transactionService->getTransactionsWithPagination($user['id'], $page, $perPage);

        $this->render('dashboard/dashboard.html.php', [
            'user' => $user,
            'transactions' => $result['transactions'],
            'pagination' => $result['pagination'],
            'solde' => $solde,
            'comptes' => $comptes,
        ]);
    }

 

    private function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }

 

    public function createCompteSecondaire()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $userId = Session::get('user')['id'];
        $telephone = $_POST['telephone'] ?? '';
        $solde = $_POST['solde'] ?? '';

        $validator = Validator::getInstance();
        $isValid = $validator->validate([
            'telephone' => $telephone,
            'solde' => $solde
        ], [
            'telephone' => ['required', ['isSenegalPhone', Lang::get('validation.phone_invalid')]],
            'solde' => ['required', Lang::get('validation.required')]
        ]);

        if (!$isValid) {
            $errors = Validator::getErrors();
            $errorMessages = implode(', ', $errors);
            Session::set('error', $errorMessages);
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $soldeFloat = (float) $solde;
        if ($soldeFloat <= 0) {
            Session::set('error', Lang::get('account.balance_initial_positive'));
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $result = $this->compteService->createCompteSecondaire($userId, $telephone, $soldeFloat);

        if ($result['success']) {
            Session::set('success', $result['message']);
        } else {
            Session::set('error', $result['message']);
        }

        $this->redirect(APP_URL . '/dashboard');
    }

    public function changerPrincipal()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $userId = Session::get('user')['id'];
        $compteId = $_POST['compte_id'] ?? '';

        // Validation avec votre classe Validator
        $validator = Validator::getInstance();
        $isValid = $validator->validate([
            'compte_id' => $compteId
        ], [
            'compte_id' => ['required']
        ]);

        if (!$isValid) {
            Session::set('error', Lang::get('account.id_required'));
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $compteIdInt = (int) $compteId;
        if ($compteIdInt <= 0) {
            Session::set('error', Lang::get('account.id_invalid'));
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $result = $this->compteService->changerComptePrincipal($userId, $compteIdInt);

        if ($result['success']) {
            Session::set('success', $result['message']);
        } else {
            Session::set('error', $result['message']);
        }

        $this->redirect(APP_URL . '/dashboard');
    }

    public function transactions()
    {
        if (!Session::isset('user')) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        $userId = Session::get('user')['id'];
        $user = Session::get('user');
        
        // Récupération et validation des filtres
        $filters = [];
        $filterData = [
            'type' => $_GET['type'] ?? '',
            'date_debut' => $_GET['date_debut'] ?? '',
            'date_fin' => $_GET['date_fin'] ?? ''
        ];

        // Validation des filtres optionnels
        if (!empty($filterData['type'])) {
            $validator = Validator::getInstance();
            $isValidType = $validator->validate([
                'type' => $filterData['type']
            ], [
                'type' => ['required']
            ]);
            
            if ($isValidType) {
                $filters['type'] = $filterData['type'];
            }
        }

        // Validation des dates si présentes
        if (!empty($filterData['date_debut'])) {
            if ($this->isValidDate($filterData['date_debut'])) {
                $filters['date_debut'] = $filterData['date_debut'];
            }
        }

        if (!empty($filterData['date_fin'])) {
            if ($this->isValidDate($filterData['date_fin'])) {
                $filters['date_fin'] = $filterData['date_fin'];
            }
        }

        // Validation cohérence des dates
        if (isset($filters['date_debut']) && isset($filters['date_fin'])) {
            if (strtotime($filters['date_debut']) > strtotime($filters['date_fin'])) {
                Session::set('error', Lang::get('transaction.date_range_invalid'));
                $this->redirect(APP_URL . '/transactions');
                exit();
            }
        }
        
        // Pagination avec validation
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        
        // Récupération des transactions avec filtres
        $result = $this->transactionService->getTransactionsWithFilters($userId, $filters, $page, $perPage);
        
        // Types de transactions disponibles
        $transactionTypes = $this->transactionService->getTransactionTypes();
        
        $this->render('transactions/transactions.html.php', [
            'user' => $user,
            'transactions' => $result['transactions'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'transactionTypes' => $transactionTypes
        ]);
    }

    /**
     * Valide si une chaîne est une date valide au format Y-m-d
     */
    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Changer la langue de l'application
     */
    public function changeLang()
    {
        $lang = $_GET['lang'] ?? 'fr';
        
        if (in_array($lang, ['fr', 'en'])) {
            Session::set('lang', $lang);
            Lang::setLang($lang);
        }
        
        // Rediriger vers la page précédente ou dashboard
        $redirect = $_GET['redirect'] ?? '/dashboard';
        $this->redirect(APP_URL . $redirect);
    }

       public function create()
    {
    }
    public function store()
    {
    }
    public function show()
    {
    }
    public function edit()
    {
    }
}