<?php
/**
 * Script pour corriger la structure de la base Railway
 * Ajoute les colonnes manquantes et les types enum
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/env.php';

try {
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "=== CORRECTION DE LA BASE RAILWAY ===\n";
    
    // 1. Créer les types enum s'ils n'existent pas
    echo "1. Création des types enum...\n";
    
    $pdo->exec("DO $$ 
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'type_compte') THEN
            CREATE TYPE type_compte AS ENUM ('ComptePrincipal', 'CompteSecondaire');
        END IF;
    END $$;");
    echo "✅ Type type_compte créé\n";
    
    $pdo->exec("DO $$ 
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'type_transaction') THEN
            CREATE TYPE type_transaction AS ENUM ('Depot', 'Retrait', 'Transfert', 'Woyofal');
        END IF;
    END $$;");
    echo "✅ Type type_transaction créé\n";
    
    // 2. Vérifier si la colonne type existe dans compte
    $checkColumn = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'compte' AND column_name = 'type'");
    if ($checkColumn->rowCount() == 0) {
        echo "2. Ajout de la colonne type dans la table compte...\n";
        $pdo->exec("ALTER TABLE compte ADD COLUMN type type_compte DEFAULT 'ComptePrincipal'");
        echo "✅ Colonne type ajoutée\n";
    } else {
        echo "2. La colonne type existe déjà\n";
    }
    
    // 3. Mettre à jour les comptes existants avec le bon type
    echo "3. Mise à jour des types de comptes...\n";
    
    // D'abord, changer la colonne en VARCHAR temporairement
    $pdo->exec("ALTER TABLE compte ALTER COLUMN type TYPE VARCHAR(50)");
    
    // Mettre à jour les valeurs vides
    $pdo->exec("UPDATE compte SET type = 'ComptePrincipal' WHERE type IS NULL OR type = '' OR type NOT IN ('ComptePrincipal', 'CompteSecondaire')");
    
    // Reconvertir en enum
    $pdo->exec("ALTER TABLE compte ALTER COLUMN type TYPE type_compte USING type::type_compte");
    
    echo "✅ Types de comptes mis à jour\n";
    
    // 4. Vérifier la structure finale
    echo "4. Vérification finale...\n";
    $checkCompte = $pdo->query("SELECT num_compte, solde, type FROM compte LIMIT 3");
    $comptes = $checkCompte->fetchAll();
    
    if ($comptes) {
        echo "✅ Comptes trouvés:\n";
        foreach ($comptes as $compte) {
            echo "  - " . $compte['num_compte'] . " : " . number_format($compte['solde']) . " FCFA (Type: " . $compte['type'] . ")\n";
        }
    } else {
        echo "❌ Aucun compte trouvé\n";
    }
    
    echo "\n=== CORRECTION TERMINÉE ===\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
