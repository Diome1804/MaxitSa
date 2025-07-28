<?php

echo "🔧 Correction Migration Railway PostgreSQL\n";
echo "==========================================\n\n";

// Configuration directe Railway
$railwayUrl = 'postgresql://postgres:IoiQfHDMYkFAXvwkHaawDOlpkKJPLslx@shuttle.proxy.rlwy.net:30832/railway';
$urlParts = parse_url($railwayUrl);

$dsn = "pgsql:host={$urlParts['host']};port={$urlParts['port']};dbname=" . ltrim($urlParts['path'], '/');

try {
    $pdo = new PDO($dsn, $urlParts['user'], $urlParts['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion Railway réussie !\n\n";
    
    // Nettoyer la base si nécessaire
    echo "🧹 Nettoyage de la base...\n";
    $pdo->exec("DROP SCHEMA public CASCADE");
    $pdo->exec("CREATE SCHEMA public");
    echo "✅ Base nettoyée\n\n";
    
    echo "🗃 Création des tables dans l'ordre correct...\n\n";
    
    // 1. Création des ENUMs
    echo "1. Création des types ENUM...\n";
    $pdo->exec("CREATE TYPE statut_enum AS ENUM ('success', 'error', 'pending')");
    $pdo->exec("CREATE TYPE type_transaction_enum AS ENUM ('depot', 'retrait', 'transfert', 'paiement', 'woyofal', 'transfert_sortant', 'transfert_entrant', 'frais_transfert', 'annulation_depot')");
    echo "   ✅ ENUMs créés\n\n";
    
    // 2. Table type_user
    echo "2. Création table type_user...\n";
    $pdo->exec("CREATE TABLE type_user (
        id SERIAL PRIMARY KEY,
        libelle VARCHAR(100) NOT NULL
    )");
    echo "   ✅ Table type_user créée\n\n";
    
    // 3. Table user
    echo "3. Création table user...\n";
    $pdo->exec("CREATE TABLE \"user\" (
        id SERIAL PRIMARY KEY,
        nom VARCHAR(100),
        prenom VARCHAR(100),
        adresse TEXT,
        num_carte_identite VARCHAR(50),
        photorecto TEXT,
        password VARCHAR(255),
        telephone VARCHAR(50),
        email VARCHAR(255),
        type_user_id INTEGER REFERENCES type_user(id)
    )");
    echo "   ✅ Table user créée\n\n";
    
    // 4. Table compte
    echo "4. Création table compte...\n";
    $pdo->exec("CREATE TABLE compte (
        id SERIAL PRIMARY KEY,
        num_compte VARCHAR(50) UNIQUE,
        solde DECIMAL(10,2) DEFAULT 0,
        user_id INTEGER REFERENCES \"user\"(id)
    )");
    echo "   ✅ Table compte créée\n\n";
    
    // 5. Table transactions
    echo "5. Création table transactions...\n";
    $pdo->exec("CREATE TABLE transactions (
        id SERIAL PRIMARY KEY,
        compte_id INTEGER REFERENCES compte(id),
        user_id INTEGER REFERENCES \"user\"(id),
        type VARCHAR(50),
        montant DECIMAL(10,2),
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expediteur_id INTEGER REFERENCES \"user\"(id),
        statut VARCHAR(20) DEFAULT 'success',
        description TEXT,
        reference VARCHAR(100),
        details TEXT
    )");
    echo "   ✅ Table transactions créée\n\n";
    
    // 6. Table Citoyen (si nécessaire)
    echo "6. Création table Citoyen...\n";
    $pdo->exec("CREATE TABLE Citoyen (
        id SERIAL PRIMARY KEY,
        client VARCHAR(255),
        service_com VARCHAR(255)
    )");
    echo "   ✅ Table Citoyen créée\n\n";
    
    echo "🌱 Insertion des données de base...\n\n";
    
    // Données type_user
    echo "   - Types d'utilisateurs...\n";
    $pdo->exec("INSERT INTO type_user (libelle) VALUES ('admin'), ('client') ON CONFLICT DO NOTHING");
    
    // Utilisateur admin
    echo "   - Utilisateur admin...\n";
    $hashedPassword = password_hash('Dakar2026', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO \"user\" (nom, prenom, telephone, email, password, type_user_id) 
                VALUES ('Admin', 'MAXITSA', '123456789', 'admin@maxitsa.com', '{$hashedPassword}', 1)
                ON CONFLICT DO NOTHING");
    
    // Utilisateur test
    echo "   - Utilisateur test...\n";
    $hashedPasswordTest = password_hash('passer123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO \"user\" (nom, prenom, telephone, email, password, type_user_id) 
                VALUES ('Test', 'User', '987654321', 'test@maxitsa.com', '{$hashedPasswordTest}', 2)
                ON CONFLICT DO NOTHING");
    
    // Comptes
    echo "   - Comptes de test...\n";
    $pdo->exec("INSERT INTO compte (num_compte, solde, user_id) 
                VALUES ('MAXITSA001', 50000.00, 1), ('MAXITSA002', 25000.00, 2)
                ON CONFLICT DO NOTHING");
    
    // Transactions d'exemple
    echo "   - Transactions d'exemple...\n";
    $pdo->exec("INSERT INTO transactions (compte_id, type, montant, statut, description) VALUES
                (1, 'depot', 10000.00, 'success', 'Dépôt initial admin'),
                (2, 'depot', 5000.00, 'success', 'Dépôt initial test'),
                (1, 'transfert_sortant', -2000.00, 'success', 'Transfert vers test'),
                (2, 'transfert_entrant', 2000.00, 'success', 'Transfert depuis admin')
                ON CONFLICT DO NOTHING");
    
    // Vérification finale
    echo "\n📊 Vérification des données créées :\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM "user"');
    $userCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM compte');
    $compteCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query('SELECT COUNT(*) FROM transactions');
    $transactionCount = $stmt->fetchColumn();
    
    echo "   - Utilisateurs: {$userCount}\n";
    echo "   - Comptes: {$compteCount}\n";
    echo "   - Transactions: {$transactionCount}\n";
    
    echo "\n🎉 Migration Railway terminée avec succès !\n";
    echo "\n🔗 Informations de connexion :\n";
    echo "   Email: admin@maxitsa.com\n";
    echo "   Password: Dakar2026\n";
    echo "\n   OU\n";
    echo "   Email: test@maxitsa.com\n";
    echo "   Password: passer123\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
