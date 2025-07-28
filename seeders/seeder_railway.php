<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\DependencyContainer;

// Configuration directe pour Railway
$databaseUrl = 'postgresql://postgres:IoiQfHDMYkFAXvwkHaawDOlpkKJPLslx@shuttle.proxy.rlwy.net:30832/railway';
$urlParts = parse_url($databaseUrl);
$dbHost = $urlParts['host'];
$dbPort = $urlParts['port'] ?? '5432';
$dbName = ltrim($urlParts['path'], '/');
$dbUser = $urlParts['user'];
$dbPassword = $urlParts['pass'];

$dsn = "pgsql:host={$dbHost};dbname={$dbName};port={$dbPort}";

//ici on defini les constantes qu on va utiliser dans notre application
define('DB_USER', $dbUser);
define('DB_PASSWORD', $dbPassword);
define('APP_URL', 'https://maxitsa.onrender.com');
define('dsn', $dsn);

// URLs des services externes
define('APPDAF_API_URL', 'https://appdaff-zwqf.onrender.com');
define('WOYOFAL_API_URL', 'https://appwoyofal.onrender.com');

echo "Configuration Railway :\n";
echo "Host: {$dbHost}\n";
echo "Database: {$dbName}\n";
echo "User: {$dbUser}\n";
echo "Port: {$dbPort}\n\n";

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données Railway\n";
    
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
    $types = ['Client', 'ServiceCom'];
    $stmtType = $pdo->prepare("INSERT INTO type_user (libelle) VALUES (?)");
    
    $typeClientId = null;
    $typeServiceId = null;
    
    foreach ($types as $type) {
        $stmtType->execute([$type]);
        $id = $pdo->lastInsertId();
        if ($type === 'Client') {
            $typeClientId = $id;
        } else {
            $typeServiceId = $id;
        }
    }
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
        ['Fatou', 'Seck', 'Grand Yoff', '1567890123456', 'recto5.png','704567891', $cryptoMiddleware('fatou456'), $typeClientId]
    ];
    $stmtUser = $pdo->prepare("INSERT INTO \"user\" (nom, prenom, adresse, num_carte_identite, photorecto,telephone, password, type_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $userIds = [];
    foreach ($users as $user) {
        $stmtUser->execute($user);
        $userIds[] = $pdo->lastInsertId();
    }
    echo "Utilisateurs insérés\n";

    // 3. Comptes avec numéros sénégalais
    $comptes = [
        ['CPT778232295001', 150000, $userIds[0]],
        ['CPT771234567001', 120000, $userIds[1]],
        ['CPT785432198001', 85000, $userIds[2]],
        ['CPT776543210001', 200000, $userIds[3]],
        ['CPT704567891001', 45000, $userIds[4]],
        ['CPT778232295002', 25000, $userIds[0]]
    ];
    $stmtCompte = $pdo->prepare("INSERT INTO compte (num_compte, solde, user_id) VALUES (?, ?, ?)");
    $compteIds = [];
    foreach ($comptes as $compte) {
        $stmtCompte->execute($compte);
        $compteIds[] = $pdo->lastInsertId();
    }
    echo "Comptes insérés\n";

    // 4. Transactions réalistes 
    $transactions = [
        ['2025-07-20 09:30:00', $compteIds[0], 15000, 'depot'],
        ['2025-07-20 10:15:00', $compteIds[1], 25000, 'depot'],
        ['2025-07-20 11:45:00', $compteIds[0], 5000, 'transfert'],
        ['2025-07-20 14:20:00', $compteIds[2], 3000, 'woyofal'],
        ['2025-07-20 16:30:00', $compteIds[3], 12000, 'transfert'],
        ['2025-07-21 08:15:00', $compteIds[1], 2500, 'retrait'],
        ['2025-07-21 12:00:00', $compteIds[4], 8000, 'depot'],
        ['2025-07-21 15:45:00', $compteIds[0], 1500, 'woyofal']
    ];
    $stmtTrx = $pdo->prepare("INSERT INTO transactions (date, compte_id, montant, type) VALUES (?, ?, ?, ?)");
    foreach ($transactions as $trx) {
        $stmtTrx->execute($trx);
    }
    echo "Transactions insérées\n";

    $pdo->commit();
    echo "Toutes les données ont été insérées avec succès dans Railway PostgreSQL.\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erreur lors de l'insertion des données : " . $e->getMessage());
}
