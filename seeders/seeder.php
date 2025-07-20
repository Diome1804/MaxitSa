<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\MiddlewareLoader;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $dsn = $_ENV['dns'] ?? "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}";
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

    // 1. Types d'utilisateurs
    $types = ['Client', 'ServiceCommercial'];
    $stmtType = $pdo->prepare("INSERT INTO type_user (libelle) VALUES (?)");
    foreach ($types as $type) {
        $stmtType->execute([$type]);
    }
    echo "Types d'utilisateur insérés\n";

    // Récupération des IDs
    $typeClientId = $pdo->query("SELECT id FROM type_user WHERE libelle = 'Client'")->fetchColumn();
    $typeServiceId = $pdo->query("SELECT id FROM type_user WHERE libelle = 'ServiceCommercial'")->fetchColumn();

    // 2. Utilisateurs
    $user = [
        ['Fallou', 'Ndiaye', 'Dakar Liberté 6', 'CNI001', 'recto1.png', 'verso1.png', MiddlewareLoader::execute('crypt', 'passer123'), '770000001', $typeClientId],
        ['Ousmane', 'Marra', 'Dakar Médina', 'CNI002', 'recto2.png', 'verso2.png', MiddlewareLoader::execute('crypt', 'passer123'), '770000002', $typeClientId],
        ['Astou', 'Mbow', 'Rufisque', 'CNI003', 'recto3.png', 'verso3.png', MiddlewareLoader::execute('crypt', 'passer123'), '770000003', $typeClientId],
        ['Admin', 'Service', 'Dakar Plateau', 'ADM001', 'admin_recto.png', 'admin_verso.png', MiddlewareLoader::execute('crypt', 'admin123'), '770000010', $typeServiceId]
    ];
    $stmtUser = $pdo->prepare("INSERT INTO users (nom, prenom, adresse, num_carte_identite, photorecto, photoverso, password, telephone, type_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $userIds = [];
    foreach ($users as $user) {
        $stmtUser->execute($user);
        $userIds[] = $pdo->lastInsertId();
    }
    echo "Utilisateurs insérés\n";

    // 3. Comptes
    $comptes = [
        ['CP001', 150000, $userIds[0], 'ComptePrincipal', '770000001'],
        ['CP002', 120000, $userIds[1], 'ComptePrincipal', '770000002'],
        ['CS001', 20000, $userIds[2], 'CompteSecondaire', '770000003']
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