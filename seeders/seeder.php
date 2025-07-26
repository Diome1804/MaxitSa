<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\MiddlewareLoader;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $dsn = $_ENV['dsn'] ?? "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}";
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données\n";
    
    // Initialiser le middleware loader
    $middlewareLoader = MiddlewareLoader::getInstance();
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

try {
    $pdo->beginTransaction();

    // Nettoyer les tables existantes
    echo "Nettoyage des tables...\n";
    $pdo->exec('TRUNCATE transactions, compte, "user", type_user RESTART IDENTITY CASCADE');
    
    // 1. Types d'utilisateurs
    $types = ['Client', 'ServiceCom'];
    $stmtType = $pdo->prepare("INSERT INTO type_user (client, service_com) VALUES (?, ?)");
    $stmtType->execute(['Client', 'ServiceCom']);
    echo "Types d'utilisateur insérés\n";

    // Récupération des IDs  
    $typeClientId = 1; // Premier type inséré
    $typeServiceId = 1; // Même ID car une seule ligne avec les deux types

    // 2. Utilisateurs
    $users = [
        ['Fallou', 'Ndiaye', 'Dakar Liberté 6', '1453555775775', 'recto1.png','778904433', MiddlewareLoader::execute('crypt', 'passer123'), $typeClientId],
        ['Abdou', 'Diallo', 'Fann', '145355577577Z', 'recto2.png','778234433', MiddlewareLoader::execute('crypt', 'Dakar2026'), $typeClientId]
    ];
    $stmtUser = $pdo->prepare("INSERT INTO \"user\" (nom, prenom, adresse, num_carte_identite, photorecto,telephone, password, type_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $userIds = [];
    foreach ($users as $user) {
        $stmtUser->execute($user);
        $userIds[] = $pdo->lastInsertId();
    }
    echo "Utilisateurs insérés\n";

    // 3. Comptes
    $comptes = [
        ['CPT17534363326919', 150000, $userIds[0], 'ComptePrincipal', '770000123'],
        ['CPT17534363326213', 120000, $userIds[1], 'ComptePrincipal', '770800002'],
        ['CPT17534363326212', 20000, $userIds[0], 'CompteSecondaire', '770000403']
    ];
    $stmtCompte = $pdo->prepare("INSERT INTO compte (num_compte, solde, user_id, type, num_telephone) VALUES (?, ?, ?, ?, ?)");
    $compteIds = [];
    foreach ($comptes as $compte) {
        $stmtCompte->execute($compte);
        $compteIds[] = $pdo->lastInsertId();
    }
    echo "Comptes insérés\n";

    // 4. Transactions
    $transactions = [
        ['2025-07-18 12:00:00', $compteIds[0], 10000, 'transfert'],
        ['2025-07-18 14:00:00', $compteIds[1], 5000, 'paiement'],
        ['2025-07-18 15:00:00', $compteIds[2], 2000, 'paiement']
    ];
    $stmtTrx = $pdo->prepare("INSERT INTO transactions (date, compte_id, montant, type) VALUES (?, ?, ?, ?)");
    foreach ($transactions as $trx) {
        $stmtTrx->execute($trx);
    }
    echo "Transactions insérées\n";

    $pdo->commit();
    echo "Toutes les données ont été insérées avec succès dans une transaction.\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erreur lors de l'insertion des données : " . $e->getMessage());
}


// "scripts": {
//     "database:migrate": "php migrations/migration.php",
//     "database:seeder":"php seeders/seeder.php"
//   }