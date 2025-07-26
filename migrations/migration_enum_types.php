<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

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

    echo "Connexion réussie à la base PostgreSQL.\n";

    // Vérifier les types d'énumération existants
    echo "Vérification des types d'énumération...\n";
    
    $stmt = $pdo->query("SELECT typname FROM pg_type WHERE typtype = 'e'");
    $enums = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Types d'énumération existants: " . implode(', ', $enums) . "\n";

    // Ajouter les nouveaux types à l'énumération type_transaction si elle existe
    if (in_array('type_transaction_enum', $enums)) {
        echo "Mise à jour de l'énumération type_transaction_enum...\n";
        
        $newTypes = [
            'depot',
            'woyofal', 
            'transfert_sortant',
            'transfert_entrant',
            'frais_transfert',
            'annulation_depot'
        ];
        
        foreach ($newTypes as $type) {
            try {
                $pdo->exec("ALTER TYPE type_transaction_enum ADD VALUE IF NOT EXISTS '$type'");
                echo "✅ Type '$type' ajouté\n";
            } catch (Exception $e) {
                echo "⚠️  Type '$type' existe déjà ou erreur: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "Création de l'énumération type_transaction_enum...\n";
        $pdo->exec("
            CREATE TYPE type_transaction_enum AS ENUM (
                'paiement', 
                'transfert', 
                'retrait', 
                'depot',
                'woyofal', 
                'transfert_sortant',
                'transfert_entrant',
                'frais_transfert',
                'annulation_depot'
            )
        ");
        echo "✅ Énumération type_transaction_enum créée\n";
    }

    // Vérifier si la colonne type utilise l'énumération
    $stmt = $pdo->query("
        SELECT column_name, data_type, udt_name 
        FROM information_schema.columns 
        WHERE table_name = 'transactions' AND column_name = 'type'
    ");
    $columnInfo = $stmt->fetch();
    
    if ($columnInfo) {
        echo "Colonne type: {$columnInfo['data_type']} ({$columnInfo['udt_name']})\n";
        
        if ($columnInfo['udt_name'] !== 'type_transaction_enum') {
            echo "Conversion de la colonne type vers l'énumération...\n";
            $pdo->exec("ALTER TABLE transactions ALTER COLUMN type TYPE type_transaction_enum USING type::type_transaction_enum");
            echo "✅ Colonne type convertie\n";
        }
    }

    echo "\n✅ Migration des types d'énumération terminée!\n";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
