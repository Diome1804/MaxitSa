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
            $this->redirect(APP_URL . '/');
            exit();
        }

        $user = $_SESSION['user'];

        $transactions = $this->transactionService->getRecentTransactions($user['id'], 10);

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

    // Ajoute cette méthode si elle n'existe pas déjà
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