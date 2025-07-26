<?php

// Migration pour ajouter le support des transactions Woyofal
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

    // Ajouter les colonnes manquantes à la table transactions pour le support Woyofal
    echo "Ajout des colonnes pour les transactions Woyofal...\n";

    // Vérifier et ajouter user_id (pour les transactions qui ne passent pas par un compte)
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                      WHERE table_name = 'transactions' AND column_name = 'user_id') THEN
            ALTER TABLE transactions ADD COLUMN user_id INTEGER REFERENCES \"user\"(id);
        END IF;
    END$$;");

    // Vérifier et ajouter reference (référence unique de la transaction)
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                      WHERE table_name = 'transactions' AND column_name = 'reference') THEN
            ALTER TABLE transactions ADD COLUMN reference VARCHAR(100);
        END IF;
    END$$;");

    // Vérifier et ajouter statut (success, error, pending)
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                      WHERE table_name = 'transactions' AND column_name = 'statut') THEN
            ALTER TABLE transactions ADD COLUMN statut VARCHAR(20) DEFAULT 'success';
        END IF;
    END$$;");

    // Vérifier et ajouter details (JSON pour stocker les détails spécifiques)
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                      WHERE table_name = 'transactions' AND column_name = 'details') THEN
            ALTER TABLE transactions ADD COLUMN details TEXT;
        END IF;
    END$$;");

    // Vérifier et ajouter date_creation (pour les nouvelles transactions)
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                      WHERE table_name = 'transactions' AND column_name = 'date_creation') THEN
            ALTER TABLE transactions ADD COLUMN date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
        END IF;
    END$$;");

    // Rendre compte_id nullable pour les transactions Woyofal
    $pdo->exec("ALTER TABLE transactions ALTER COLUMN compte_id DROP NOT NULL;");

    echo "Migration Woyofal exécutée avec succès !\n";

} catch (PDOException $e) {
    echo "Erreur lors de la migration Woyofal : " . $e->getMessage() . "\n";
}
