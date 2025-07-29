<?php
/**
 * Script pour recréer complètement la base de données Railway
 * Basé sur la structure locale qui fonctionne
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/env.php';

try {
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "=== RESET COMPLET DE LA BASE RAILWAY ===\n";
    
    // 1. Supprimer toutes les tables et types existants
    echo "1. Suppression des tables existantes...\n";
    $pdo->exec("DROP TABLE IF EXISTS transactions CASCADE");
    $pdo->exec("DROP TABLE IF EXISTS compte CASCADE");
    $pdo->exec("DROP TABLE IF EXISTS \"user\" CASCADE");
    $pdo->exec("DROP TABLE IF EXISTS type_user CASCADE");
    $pdo->exec("DROP TABLE IF EXISTS type_service CASCADE");
    
    // Supprimer les types enum existants
    $pdo->exec("DROP TYPE IF EXISTS type_compte CASCADE");
    $pdo->exec("DROP TYPE IF EXISTS type_transaction CASCADE");
    echo "✅ Tables supprimées\n";
    
    // 2. Créer les types ENUM
    echo "2. Création des types ENUM...\n";
    $pdo->exec("CREATE TYPE type_compte AS ENUM ('ComptePrincipal', 'CompteSecondaire')");
    $pdo->exec("CREATE TYPE type_transaction AS ENUM ('Depot', 'Retrait', 'Transfert', 'Woyofal')");
    echo "✅ Types ENUM créés\n";
    
    // 3. Créer les tables dans l'ordre des dépendances
    echo "3. Création des tables...\n";
    
    // Table: type_user
    $pdo->exec("CREATE TABLE type_user (
        id SERIAL PRIMARY KEY,
        type VARCHAR(50) UNIQUE
    )");
    echo "✅ Table type_user créée\n";
    
    // Table: type_service
    $pdo->exec("CREATE TABLE type_service (
        id SERIAL PRIMARY KEY,
        nom VARCHAR(100),
        description TEXT
    )");
    echo "✅ Table type_service créée\n";
    
    // Table: user
    $pdo->exec("CREATE TABLE \"user\" (
        id SERIAL PRIMARY KEY,
        nom VARCHAR(100),
        prenom VARCHAR(100),
        adresse TEXT,
        num_carte_identite VARCHAR(50) UNIQUE,
        photorecto VARCHAR(255),
        photoverso VARCHAR(255),
        telephone VARCHAR(50) UNIQUE,
        password VARCHAR(255),
        type_id INTEGER REFERENCES type_user(id),
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Table user créée\n";
    
    // Table: compte
    $pdo->exec("CREATE TABLE compte (
        id SERIAL PRIMARY KEY,
        num_compte VARCHAR(50) UNIQUE,
        solde DECIMAL(15,2) DEFAULT 0,
        user_id INTEGER REFERENCES \"user\"(id),
        type type_compte DEFAULT 'ComptePrincipal',
        num_telephone VARCHAR(50),
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Table compte créée\n";
    
    // Table: transactions (structure complète)
    $pdo->exec("CREATE TABLE transactions (
        id SERIAL PRIMARY KEY,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        compte_id INTEGER REFERENCES compte(id) NULL,
        user_id INTEGER REFERENCES \"user\"(id) NULL,
        montant DECIMAL(15,2),
        type type_transaction,
        reference VARCHAR(100) UNIQUE,
        statut VARCHAR(20) DEFAULT 'success',
        description TEXT,
        details TEXT,
        expediteur_id INTEGER REFERENCES \"user\"(id) NULL,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Table transactions créée\n";
    
    // 4. Créer les index pour les performances
    echo "4. Création des index...\n";
    $pdo->exec("CREATE INDEX idx_user_telephone ON \"user\"(telephone)");
    $pdo->exec("CREATE INDEX idx_compte_user_id ON compte(user_id)");
    $pdo->exec("CREATE INDEX idx_compte_type ON compte(type)");
    $pdo->exec("CREATE INDEX idx_transactions_user_id ON transactions(user_id)");
    $pdo->exec("CREATE INDEX idx_transactions_compte_id ON transactions(compte_id)");
    $pdo->exec("CREATE INDEX idx_transactions_type ON transactions(type)");
    $pdo->exec("CREATE INDEX idx_transactions_statut ON transactions(statut)");
    $pdo->exec("CREATE INDEX idx_transactions_expediteur_id ON transactions(expediteur_id)");
    echo "✅ Index créés\n";
    
    // 5. Insérer les données de base
    echo "5. Insertion des données de base...\n";
    
    // Types d'utilisateurs
    $pdo->exec("INSERT INTO type_user (type) VALUES ('client'), ('serviceCom')");
    $typeClientId = 1;
    $typeServiceId = 2;
    echo "✅ Types d'utilisateurs insérés\n";
    
    // Types de services
    $pdo->exec("INSERT INTO type_service (nom, description) VALUES 
        ('Woyofal', 'Service d''achat d''électricité'),
        ('AppDAF', 'Service de transfert d''argent')");
    echo "✅ Types de services insérés\n";
    
    echo "\n=== BASE DE DONNÉES RESET AVEC SUCCÈS ===\n";
    echo "La structure est maintenant identique à votre base locale.\n";
    echo "Vous pouvez maintenant exécuter le seeder :\n";
    echo "php seeders/seeder.php\n\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
