<?php

// Migration pour ajouter le support des dépôts et transferts
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

    // Ajouter les colonnes manquantes à la table transactions pour le support des dépôts
    echo "Ajout des colonnes pour les dépôts et transferts...\n";

    // Vérifier et ajouter expediteur_id (pour identifier qui fait le dépôt)
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                      WHERE table_name = 'transactions' AND column_name = 'expediteur_id') THEN
            ALTER TABLE transactions ADD COLUMN expediteur_id INTEGER REFERENCES \"user\"(id);
        END IF;
    END$$;");

    // Vérifier et ajouter description (description de la transaction)
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                      WHERE table_name = 'transactions' AND column_name = 'description') THEN
            ALTER TABLE transactions ADD COLUMN description TEXT;
        END IF;
    END$$;");

    // Mettre à jour la méthode updateStatus dans TransactionRepository si nécessaire
    echo "Vérifiant la structure de la table transactions...\n";
    
    // Ajouter un index sur expediteur_id pour améliorer les performances
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_transactions_expediteur_id ON transactions(expediteur_id);");
    
    // Ajouter un index sur statut pour améliorer les performances des requêtes d'annulation
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_transactions_statut ON transactions(statut);");

    echo "Migration dépôts exécutée avec succès !\n";

} catch (PDOException $e) {
    echo "Erreur lors de la migration dépôts : " . $e->getMessage() . "\n";
}
