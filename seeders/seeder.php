<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\DependencyContainer;

// Charger la configuration avec la même logique que l'application
require_once __DIR__ . '/../app/config/env.php';

try {
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données\n";
    echo "DSN: " . dsn . "\n";
    echo "User: " . DB_USER . "\n";
    
    // Initialiser le container de dépendances
    $container = DependencyContainer::getInstance();
    $cryptoMiddleware = $container->get('App\Core\Middlewares\CryptPassword');
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

try {
    $pdo->beginTransaction();

    // Nettoyer les tables existantes
    echo "Nettoyage des tables...\n";
    $pdo->exec('TRUNCATE transactions, compte, "user", type_user RESTART IDENTITY CASCADE');
    
    // 1. Types d'utilisateurs
    $stmtType = $pdo->prepare("INSERT INTO type_user (type) VALUES (?)");
    $stmtType->execute(['client']);
    $typeClientId = $pdo->lastInsertId();
    $stmtType->execute(['serviceCom']);
    $typeServiceId = $pdo->lastInsertId();
    echo "Types d'utilisateur insérés\n";

    // 2. Utilisateurs avec données sénégalaises réalistes
    // CONNEXION: Numéro de téléphone + Mot de passe
    echo "\n=== COMPTES DE TEST CRÉÉS ===\n";
    echo "Téléphone: 778232295 | Mot de passe: passer123 | Nom: Fallou Ndiaye\n";
    echo "Téléphone: 771234567 | Mot de passe: Dakar2026 | Nom: Abdou Diallo\n";
    echo "Téléphone: 785432198 | Mot de passe: aminata123 | Nom: Aminata Fall\n";
    echo "Téléphone: 776543210 | Mot de passe: ousmane2024 | Nom: Ousmane Ba\n";
    echo "Téléphone: 704567891 | Mot de passe: fatou456 | Nom: Fatou Seck\n";
    echo "==============================\n\n";
    
    $users = [
        ['Fallou', 'Ndiaye', 'Dakar Liberté 6 Extension', '1987654321098', 'recto1.png','778232295', $cryptoMiddleware('passer123'), $typeClientId],
        ['Abdou', 'Diallo', 'Fann Résidence', '1456789012345', 'recto2.png','771234567', $cryptoMiddleware('Dakar2026'), $typeClientId],
        ['Aminata', 'Fall', 'Plateau Médina', '1234567890123', 'recto3.png','785432198', $cryptoMiddleware('aminata123'), $typeClientId],
        ['Ousmane', 'Ba', 'Parcelles Assainies U10', '1345678901234', 'recto4.png','776543210', $cryptoMiddleware('ousmane2024'), $typeClientId],
        ['Fatou', 'Seck', 'Grand Yoff', '1567890123456', 'recto5.png','704567891', $cryptoMiddleware('passer456'), $typeClientId],
    ];
    $stmtUser = $pdo->prepare("INSERT INTO \"user\" (nom, prenom, adresse, num_carte_identite, photorecto,telephone, password, type_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $userIds = [];
    foreach ($users as $user) {
        $stmtUser->execute($user);
        $userIds[] = $pdo->lastInsertId();
    }
    echo "Utilisateurs insérés\n";

    // 3. Comptes avec numéros sénégalais
    $comptes = [
        ['CPT778232295001', 150000, $userIds[0], 'ComptePrincipal', '778232295'],
        ['CPT771234567001', 120000, $userIds[1], 'ComptePrincipal', '771234567'],
        ['CPT785432198001', 85000, $userIds[2], 'ComptePrincipal', '785432198'],
        ['CPT776543210001', 200000, $userIds[3], 'ComptePrincipal', '776543210'],
        ['CPT704567891001', 45000, $userIds[4], 'ComptePrincipal', '704567891'],
        ['CPT778232295002', 25000, $userIds[0], 'CompteSecondaire', '778232295']
    ];
    $stmtCompte = $pdo->prepare("INSERT INTO compte (num_compte, solde, user_id, type, num_telephone) VALUES (?, ?, ?, ?, ?)");
    $compteIds = [];
    foreach ($comptes as $compte) {
        $stmtCompte->execute($compte);
        $compteIds[] = $pdo->lastInsertId();
    }
    echo "Comptes insérés\n";

    // 4. Transactions réalistes 
    $transactions = [
        ['2025-07-20 09:30:00', $compteIds[0], 15000, 'Depot'],
        ['2025-07-20 10:15:00', $compteIds[1], 25000, 'Depot'],
        ['2025-07-20 11:45:00', $compteIds[0], 5000, 'Transfert'],
        ['2025-07-20 14:20:00', $compteIds[2], 3000, 'Woyofal'],
        ['2025-07-20 16:30:00', $compteIds[3], 12000, 'Transfert'],
        ['2025-07-21 08:15:00', $compteIds[1], 2500, 'Retrait'],
        ['2025-07-21 12:00:00', $compteIds[4], 8000, 'Depot'],
        ['2025-07-21 15:45:00', $compteIds[0], 1500, 'Woyofal']
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