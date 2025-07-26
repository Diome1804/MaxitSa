<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/env.php';

try {
    $pdo = new PDO(
        dsn,
        DB_USER, 
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "Connexion réussie à la base de données\n";

    // Vérifier les transactions existantes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM transactions");
    $result = $stmt->fetch();
    echo "Nombre de transactions existantes: " . $result['count'] . "\n";

    // Récupérer les comptes existants
    $stmt = $pdo->query("SELECT id, user_id, num_compte, type FROM compte LIMIT 3");
    $comptes = $stmt->fetchAll();

    if (empty($comptes)) {
        echo "Aucun compte trouvé. Veuillez d'abord exécuter les migrations.\n";
        exit(1);
    }

    echo "Comptes trouvés:\n";
    foreach ($comptes as $compte) {
        echo "- ID: {$compte['id']}, User: {$compte['user_id']}, Numéro: {$compte['num_compte']}, Type: {$compte['type']}\n";
    }

    $userId = $comptes[0]['user_id'];
    $compteId = $comptes[0]['id'];

    // Ajouter quelques transactions de test
    echo "\nAjout de transactions de test...\n";

    // Transaction de dépôt
    $stmt = $pdo->prepare("
        INSERT INTO transactions (compte_id, type, montant, date, expediteur_id, statut, description, reference) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $compteId, 
        'depot', 
        25000, 
        date('Y-m-d H:i:s'), 
        $userId, 
        'success', 
        'Dépôt de test', 
        'DEP' . date('YmdHis')
    ]);
    echo "✅ Transaction de dépôt ajoutée\n";

    // Transaction Woyofal (structure directe)
    $stmt = $pdo->prepare("
        INSERT INTO transactions (user_id, type, montant, date, reference, statut, details, date_creation) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        'woyofal',
        5000,
        date('Y-m-d H:i:s'), // Ajouter la date obligatoire
        'WYF' . date('YmdHis'),
        'success',
        json_encode([
            'compteur' => '963852741',
            'code' => 'WYF12345678',
            'client' => 'Test Client',
            'nbreKwt' => 49.02,
            'tranche' => 'Tranche 2 - Normal',
            'prix' => 102
        ]),
        date('Y-m-d H:i:s')
    ]);
    echo "✅ Transaction Woyofal ajoutée\n";

    // Transaction de transfert sortant
    if (count($comptes) > 1) {
        $stmt = $pdo->prepare("
            INSERT INTO transactions (compte_id, type, montant, date, statut, description, reference) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $compteId,
            'transfert_sortant',
            -10000,
            date('Y-m-d H:i:s'),
            'success',
            'Transfert vers ' . $comptes[1]['num_compte'],
            'TRF' . date('YmdHis')
        ]);
        echo "✅ Transaction de transfert sortant ajoutée\n";

        // Transaction de transfert entrant
        $stmt->execute([
            $comptes[1]['id'],
            'transfert_entrant',
            10000,
            date('Y-m-d H:i:s'),
            'success',
            'Transfert depuis ' . $comptes[0]['num_compte'],
            'TRF' . date('YmdHis')
        ]);
        echo "✅ Transaction de transfert entrant ajoutée\n";
    }

    // Vérifier le résultat
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM transactions");
    $result = $stmt->fetch();
    echo "\nNombre total de transactions après ajout: " . $result['count'] . "\n";

    // Afficher quelques transactions pour vérification
    echo "\nDernières transactions:\n";
    $stmt = $pdo->prepare("
        SELECT t.*, c.num_compte 
        FROM transactions t 
        LEFT JOIN compte c ON t.compte_id = c.id 
        ORDER BY COALESCE(t.date_creation, t.date) DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $transactions = $stmt->fetchAll();
    
    foreach ($transactions as $trans) {
        echo "- {$trans['type']}: {$trans['montant']} FCFA (Compte: " . ($trans['num_compte'] ?? 'Direct') . ")\n";
    }

    echo "\n✅ Transactions de test ajoutées avec succès!\n";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
