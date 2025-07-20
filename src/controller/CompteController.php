<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\CompteService;
use Src\Service\TransactionService;
use App\Core\App;

class CompteController extends AbstractController
{
    private TransactionService $transactionService;
    private CompteService $compteService;

    public function __construct()
    {
        parent::__construct();
        $this->transactionService = App::getDependency('services', 'transactionServ');
        $this->compteService = App::getDependency('services', 'compteServ');
    }

    public function index()
    {

        if (!isset($_SESSION['user'])) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        $user = $_SESSION['user'];

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

    public function createCompteSecondaire()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $telephone = $_POST['telephone'] ?? '';
        $solde = (float) ($_POST['solde'] ?? 0);

        if (empty($telephone) || $solde <= 0) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs correctement';
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $result = $this->compteService->createCompteSecondaire($userId, $telephone, $solde);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        $this->redirect(APP_URL . '/dashboard');
    }

    public function changerPrincipal()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $compteId = (int) ($_POST['compte_id'] ?? 0);

        if ($compteId <= 0) {
            $_SESSION['error'] = 'ID de compte invalide';
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }

        $result = $this->compteService->changerComptePrincipal($userId, $compteId);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        $this->redirect(APP_URL . '/dashboard');
    }

    public function transactions()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect(APP_URL . '/');
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $user = $_SESSION['user'];
        
        // Récupération des filtres
        $filters = [];
        if (!empty($_GET['type'])) {
            $filters['type'] = $_GET['type'];
        }
        if (!empty($_GET['date_debut'])) {
            $filters['date_debut'] = $_GET['date_debut'];
        }
        if (!empty($_GET['date_fin'])) {
            $filters['date_fin'] = $_GET['date_fin'];
        }
        
        // Pagination
        $page = (int) ($_GET['page'] ?? 1);
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
}