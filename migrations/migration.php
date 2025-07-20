<?php

// Configuration de la connexion
$host = 'localhost';
$dbname = 'ton_nom_de_base';
$user = 'ton_utilisateur';
$password = 'ton_mot_de_passe';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion réussie à la base PostgreSQL.\n";

    // Création des ENUMS si non existants
    $pdo->exec("DO $$
    BEGIN
        IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'type_transaction') THEN
            CREATE TYPE type_transaction AS ENUM ('paiement', 'transfert');
        END IF;

        IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'type_compte') THEN
            CREATE TYPE type_compte AS ENUM ('ComptePrincipal', 'CompteSecondaire');
        END IF;
    END$$;");

    // Table: type_user
    $pdo->exec("CREATE TABLE IF NOT EXISTS type_user (
        id SERIAL PRIMARY KEY,
        client VARCHAR(255),
        service_com VARCHAR(255)
    );");

    // Table: users
    $pdo->exec("CREATE TABLE IF NOT EXISTS user (
        id SERIAL PRIMARY KEY,
        nom VARCHAR(100),
        prenom VARCHAR(100),
        adresse TEXT,
        num_carte_identite VARCHAR(50),
        photorecto TEXT,
        photoverso TEXT,
        password VARCHAR(255),
        telephone VARCHAR(50),
        type_id INTEGER REFERENCES type_user(id)
    );");

    // Table: compte
    $pdo->exec("CREATE TABLE IF NOT EXISTS compte (
        id SERIAL PRIMARY KEY,
        num_compte VARCHAR(50) UNIQUE,
        solde DECIMAL(15,2),
        user_id INTEGER REFERENCES users(id),
        type type_compte
        num_telephone VARCHAR(50),
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
