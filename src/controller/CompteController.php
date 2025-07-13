<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\TransactionService;
use App\Core\App;

class CompteController extends AbstractController 
{
    private TransactionService $transactionService;

    public function __construct()
    {
        parent::__construct();
        $this->transactionService = App::getDependency('services', 'transactionServ');
    }

    public function index() 
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            exit();
        }

        $user = $_SESSION['user'];
        
        // ✅ RÉCUPÉRER LES TRANSACTIONS
        $transactions = $this->transactionService->getRecentTransactions($user['id'], 10);
        
        // ✅ FORMATER LES TRANSACTIONS POUR L'AFFICHAGE
        $formattedTransactions = [];
        foreach ($transactions as $transaction) {
            $formattedTransactions[] = $this->transactionService->formatTransactionForDisplay($transaction);
        }

        error_log("Transactions trouvées: " . count($formattedTransactions));
        
        $this->render('dashboard/dashboard.html.php', [
            'user' => $user,
            'transactions' => $formattedTransactions
        ]);
    }

    public function create() {}
    public function store() {}
    public function show() {}
    public function edit() {}
}