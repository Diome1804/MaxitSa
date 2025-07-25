<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\CompteService;
use Src\Service\TransactionService;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Lang;

class CompteController extends AbstractController
{
    private TransactionService $transactionService;
    private CompteService $compteService;
    private Validator $validator;

    public function __construct(Session $session, TransactionService $transactionService, CompteService $compteService, Validator $validator)
    {
        parent::__construct($session);
        $this->transactionService = $transactionService;
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
        $validator = $this->validator;
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
        $validator = $this->validator;
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
        $validator = $this->validator;
        $filters = $validator->validateTransactionFilters($filterData);

        if (!$validator->validateDateCoherence($filters)) {
            Session::set('error', Lang::get('transaction.date_range_invalid'));
            $this->redirect(APP_URL . '/transactions');
            exit();
        }
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
    private function getFilterData(): array
    {
        return [
            'type' => $_GET['type'] ?? '',
            'date_debut' => $_GET['date_debut'] ?? '',
            'date_fin' => $_GET['date_fin'] ?? ''
        ];
    }
    private function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }
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