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

        $solde = $this->compteService->getSoldePrincipalByUserId($user['id']);

        $transactions = $this->transactionService->getRecentTransactions($user['id'], 10);

        $formattedTransactions = [];
        foreach ($transactions as $transaction) {
            $formattedTransactions[] = $this->transactionService->formatTransactionForDisplay($transaction);
        }

        $this->render('dashboard/dashboard.html.php', [
            'user' => $user,
            'transactions' => $formattedTransactions,
            'solde' => $solde,
        ]);
    }

    
    private function redirect(string $url)
                {
                    header("Location: $url");
                    exit;
                }

    public function create() {}
    public function store() {}
    public function show() {}
    public function edit() {}
}