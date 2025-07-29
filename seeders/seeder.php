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
    
    // Récupération des CNI depuis l'API AppDAF
    echo "Récupération des CNI depuis l'API AppDAF...\n";
    $validCNIs = [];
    $testCNIs = ['1234567890123', '1234567890124', '1234567890125', '1234567890126', '1234567890127'];
    
    foreach ($testCNIs as $testCNI) {
        $url = APPDAF_API_URL . '/api/citoyen/rechercher';
        $data = ['nci' => $testCNI];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $result) {
            $response = json_decode($result, true);
            if (isset($response['statut']) && $response['statut'] === 'success' && isset($response['data'])) {
                $validCNIs[] = [
                    'cni' => $testCNI,
                    'nom' => $response['data']['nom'] ?? 'Nom',
                    'prenom' => $response['data']['prenom'] ?? 'Prenom'
                ];
                echo "✅ CNI $testCNI trouvé: " . $response['data']['nom'] . " " . $response['data']['prenom'] . "\n";
            }
        }
        
        // Délai pour éviter de surcharger l'API
        usleep(500000); // 0.5 seconde
    }
    
    if (empty($validCNIs)) {
        echo "❌ Aucun CNI valide trouvé, utilisation de CNI fictifs...\n";
        $users = [
            ['Fallou', 'Ndiaye', 'Dakar Liberté 6 Extension', '9876543210987', 'recto1.png','778232295', $cryptoMiddleware('passer123'), $typeClientId],
            ['Abdou', 'Diallo', 'Fann Résidence', '9876543210988', 'recto2.png','771234567', $cryptoMiddleware('Dakar2026'), $typeClientId],
            ['Aminata', 'Fall', 'Plateau Médina', '9876543210989', 'recto3.png','785432198', $cryptoMiddleware('aminata123'), $typeClientId],
            ['Ousmane', 'Ba', 'Parcelles Assainies U10', '9876543210990', 'recto4.png','776543210', $cryptoMiddleware('ousmane2024'), $typeClientId],
            ['Fatou', 'Seck', 'Grand Yoff', '9876543210991', 'recto5.png','704567891', $cryptoMiddleware('fatou456'), $typeClientId],
        ];
    } else {
        echo "✅ " . count($validCNIs) . " CNI valides récupérés depuis AppDAF\n";
        $users = [];
        $telephones = ['778232295', '771234567', '785432198', '776543210', '704567891'];
        $passwords = ['passer123', 'Dakar2026', 'aminata123', 'ousmane2024', 'fatou456'];
        $adresses = [
            'Dakar Liberté 6 Extension',
            'Fann Résidence', 
            'Plateau Médina',
            'Parcelles Assainies U10',
            'Grand Yoff'
        ];
        
        for ($i = 0; $i < min(count($validCNIs), 5); $i++) {
            $cniData = $validCNIs[$i];
            $users[] = [
                $cniData['nom'],
                $cniData['prenom'], 
                $adresses[$i],
                $cniData['cni'],
                'recto' . ($i + 1) . '.png',
                $telephones[$i],
                $cryptoMiddleware($passwords[$i]),
                $typeClientId
            ];
        }
    }
    $stmtUser = $pdo->prepare("INSERT INTO \"user\" (nom, prenom, adresse, num_carte_identite, photorecto,telephone, password, type_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $userIds = [];
    $telephones = ['778232295', '771234567', '785432198', '776543210', '704567891'];
    $passwords = ['passer123', 'Dakar2026', 'aminata123', 'ousmane2024', 'fatou456'];
    
    echo "\n=== COMPTES DE TEST CRÉÉS ===\n";
    foreach ($users as $index => $user) {
        $stmtUser->execute($user);
        $userIds[] = $pdo->lastInsertId();
        echo "Téléphone: {$telephones[$index]} | Mot de passe: {$passwords[$index]} | Nom: {$user[0]} {$user[1]} | CNI: {$user[3]}\n";
    }
    echo "==============================\n";
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