<?php

// Charger les variables d'environnement
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

try {
    // Utiliser les mêmes constantes que votre application
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

    // Création des ENUMS si non existants
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'statut_enum') THEN
            CREATE TYPE statut_enum AS ENUM ('success', 'error');
        END IF;
    END$$;");

    // Table: type_user
    $pdo->exec("CREATE TABLE IF NOT EXISTS Citoyen (
        id SERIAL PRIMARY KEY,
        client VARCHAR(255),
        service_com VARCHAR(255)
    );");

    // Table: user
    $pdo->exec("CREATE TABLE IF NOT EXISTS \"user\" (
        id SERIAL PRIMARY KEY,
        nom VARCHAR(100),
        prenom VARCHAR(100),
        adresse TEXT,
        num_carte_identite VARCHAR(50),
        photorecto TEXT,
        password VARCHAR(255),
        telephone VARCHAR(50),
        type_id INTEGER REFERENCES type_user(id)
    );");

    // Table: compte
    $pdo->exec("CREATE TABLE IF NOT EXISTS compte (
        id SERIAL PRIMARY KEY,
        num_compte VARCHAR(50) UNIQUE,
        solde DECIMAL(15,2),
        user_id INTEGER REFERENCES \"user\"(id),
        type type_compte,
        num_telephone VARCHAR(50)
    );");

    // Table: transactions
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id SERIAL PRIMARY KEY,
        date TIMESTAMP,
        compte_id INTEGER REFERENCES compte(id),
        montant DECIMAL(15,2),
        type type_transaction
    );");

    echo "Migration exécutée avec succès !\n";

} catch (PDOException $e) {
    echo "Erreur lors de la connexion ou de la migration : " . $e->getMessage() . "\n";
}


// "scripts": {
//     "database:migrate": "php migrations/migration.php"
//   }