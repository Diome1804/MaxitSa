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

        $transactions = $this->transactionService->getLatestTransactions($user['id'], 10);

        $this->render('dashboard/dashboard.html.php', [
            'user' => $user,
            'transactions' => $transactions,
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

        $filterData = $this->getFilterData();
        $filters = $this->validateFilters($filterData);
        $this->validateDateCoherence($filters);

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $result = $this->transactionService->getTransactionsWithFilters($userId, $filters, $page, $perPage);
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
     * Récupérer les données de filtres depuis les paramètres GET
     */
    private function getFilterData(): array
    {
        return [
            'type' => $_GET['type'] ?? '',
            'date_debut' => $_GET['date_debut'] ?? '',
            'date_fin' => $_GET['date_fin'] ?? ''
        ];
    }

    /**
     * Valider les filtres et retourner les filtres valides
     */
    private function validateFilters(array $filterData): array
    {
        $filters = [];
        
        // Validation du filtre type
        if (!empty($filterData['type'])) {
            $filters = array_merge($filters, $this->validateTypeFilter($filterData['type']));
        }
        
        // Validation des filtres de dates
        if (!empty($filterData['date_debut']) || !empty($filterData['date_fin'])) {
            $filters = array_merge($filters, $this->validateDateFilters($filterData));
        }
        
        return $filters;
    }

    /**
     * Valider le filtre de type
     */
    private function validateTypeFilter(string $type): array
    {
        $validator = Validator::getInstance();
        $isValidType = $validator->validate(['type' => $type], ['type' => ['required']]);
        
        return $isValidType ? ['type' => $type] : [];
    }

    /**
     * Valider les filtres de dates
     */
    private function validateDateFilters(array $filterData): array
    {
        $validator = Validator::getInstance();
        $dateValidation = [];
        $dateRules = [];
        
        if (!empty($filterData['date_debut'])) {
            $dateValidation['date_debut'] = $filterData['date_debut'];
            $dateRules['date_debut'] = ['date'];
        }
        
        if (!empty($filterData['date_fin'])) {
            $dateValidation['date_fin'] = $filterData['date_fin'];
            $dateRules['date_fin'] = ['date'];
        }
        
        if ($validator->validate($dateValidation, $dateRules)) {
            $validDates = [];
            if (!empty($filterData['date_debut'])) {
                $validDates['date_debut'] = $filterData['date_debut'];
            }
            if (!empty($filterData['date_fin'])) {
                $validDates['date_fin'] = $filterData['date_fin'];
            }
            return $validDates;
        }
        
        return [];
    }

    /**
     * Valider la cohérence des dates (début <= fin)
     */
    private function validateDateCoherence(array $filters): void
    {
        if (isset($filters['date_debut']) && isset($filters['date_fin'])) {
            if (strtotime($filters['date_debut']) > strtotime($filters['date_fin'])) {
                Session::set('error', Lang::get('transaction.date_range_invalid'));
                $this->redirect(APP_URL . '/transactions');
                exit();
            }
        }
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