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

    echo "Test d'insertion de transaction Woyofal...\n";

    // Simuler une transaction Woyofal comme le ferait le WoyofalService
    $transactionData = [
        'user_id' => 1,
        'type' => 'woyofal',
        'montant' => 5000,
        'date' => date('Y-m-d H:i:s'),
        'reference' => 'WYF' . date('YmdHis') . rand(100, 999),
        'statut' => 'success',
        'details' => json_encode([
            'compteur' => '963852741',
            'code' => 'WYF87654321',
            'client' => 'Test Client Woyofal',
            'nbreKwt' => 49.02,
            'tranche' => 'Tranche 2 - Normal',
            'prix' => 102
        ]),
        'date_creation' => date('Y-m-d H:i:s')
    ];

    // Insérer directement avec la requête mise à jour
    $sql = "
        INSERT INTO transactions (user_id, type, montant, date, reference, statut, details, date_creation) 
        VALUES (:user_id, :type, :montant, :date, :reference, :statut, :details, :date_creation)
    ";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($transactionData);
    
    if ($result) {
        $transactionId = $pdo->lastInsertId();
        echo "✅ Transaction Woyofal insérée avec l'ID: $transactionId\n";
        
        // Vérifier qu'elle est bien récupérée par les requêtes UNION
        echo "\nVérification avec la requête UNION...\n";
        
        $sql = "
            (
                SELECT t.*, c.num_compte, t.date as date_transaction, 'compte' as source_type
                FROM transactions t 
                INNER JOIN compte c ON t.compte_id = c.id 
                WHERE c.user_id = :user_id AND t.compte_id IS NOT NULL
            )
            UNION ALL
            (
                SELECT t.*, NULL as num_compte, 
                       COALESCE(t.date_creation, t.date) as date_transaction, 
                       'direct' as source_type
                FROM transactions t 
                WHERE t.user_id = :user_id AND t.user_id IS NOT NULL
            )
            ORDER BY date_transaction DESC 
            LIMIT 5
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => 1]);
        $transactions = $stmt->fetchAll();
        
        echo "Transactions trouvées: " . count($transactions) . "\n";
        foreach ($transactions as $trans) {
            echo "- {$trans['type']}: {$trans['montant']} FCFA";
            if ($trans['num_compte']) {
                echo " (Compte: {$trans['num_compte']})";
            } else {
                echo " (Direct)";
            }
            echo " - Date: {$trans['date_transaction']}\n";
        }
        
    } else {
        echo "❌ Erreur lors de l'insertion\n";
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
