-- Supprimer si déjà existants
DROP TABLE IF EXISTS transactions, compte, "user", typeuser CASCADE;
DROP TYPE IF EXISTS type_user_enum, type_transaction_enum, type_compte_enum;

-- 1. Création des types ENUM
CREATE TYPE type_user_enum AS ENUM ('client', 'serviceCom');
CREATE TYPE type_transaction_enum AS ENUM ('paiement', 'transfert');
CREATE TYPE type_compte_enum AS ENUM ('ComptePrincipal', 'CompteSecondaire');

-- 2. Table typeuser
CREATE TABLE typeuser (
    id SERIAL PRIMARY KEY,
    type type_user_enum NOT NULL
);
select * from compte ;
-- 3. Table user
CREATE TABLE "user" (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    adresse TEXT,
    num_carte_identite VARCHAR(50),
    photorecto TEXT,
    photoverso TEXT,
    telephone VARCHAR(20),
    password TEXT,
    type_id INT REFERENCES typeuser(id)
);

select * from user ;

-- 4. Table compte
CREATE TABLE compte (
    id SERIAL PRIMARY KEY,
    num_compte VARCHAR(50) UNIQUE NOT NULL,
    solde NUMERIC(15, 2) DEFAULT 0.00,
    user_id INT NOT NULL REFERENCES "user"(id) ON DELETE CASCADE,
    type type_compte_enum NOT NULL

	ALTER TABLE compte ADD COLUMN num_telephone VARCHAR(20);

);
select * from transactions ;
-- 5. Table transactions
CREATE TABLE transactions (
    id SERIAL PRIMARY KEY,
    date DATE NOT NULL,
    compte_id INT NOT NULL REFERENCES compte(id) ON DELETE CASCADE,
    montant NUMERIC(15, 2) NOT NULL,
    type type_transaction_enum NOT NULL
);
select * from compte ;
INSERT INTO transactions (date, compte_id, montant, type) VALUES
('2025-07-05', 8, 15000.00, 'paiement'),
('2025-07-06', 8, 30000.00, 'transfert'),
('2025-07-06', 8, 10000.00, 'paiement'),
('2025-07-07', 8, 25000.00, 'transfert'),
('2025-07-08', 8, 45000.00, 'paiement'),
('2025-07-08', 8, 15000.00, 'paiement'),
('2025-07-09', 8, 50000.00, 'transfert'),
('2025-07-09', 8, 20000.00, 'transfert'),
('2025-07-10', 8, 35000.00, 'paiement'),
('2025-07-10', 8, 30000.00, 'paiement');

-- 6. Insertion des types d'utilisateurs
INSERT INTO typeuser (type) VALUES 
('client'), 
('serviceCom');

-- 7. Insertion d'utilisateurs
INSERT INTO "user" (nom, prenom, adresse, num_carte_identite, photorecto, photoverso, telephone, password, type_id) VALUES
('Diop', 'Fatou', 'Dakar', '1234567890', 'photo1recto.png', 'photo1verso.png', '771234567', 'pass123', 1),
('Ndoye', 'Amadou', 'Thies', '9876543210', 'photo2recto.png', 'photo2verso.png', '776543210', 'pass456', 2);
select * from compte ;
-- 8. Insertion de comptes
INSERT INTO compte (num_compte, solde, user_id, type, num_telephone) VALUES
('CPT1001', 500000.00, 1, 'ComptePrincipal'),
('CPT1002', 200000.00, 2, 'CompteSecondaire'),
('CPT1001', 300000.00, 1, 'ComptePrincipale',771234567);

-- 9. Insertion de transactions
INSERT INTO transactions (date, compte_id, montant, type) VALUES
('2025-07-01', 1, 25000.00, 'paiement'),
('2025-07-03', 1, 100000.00, 'transfert'),
('2025-07-04', 2, 50000.00, 'paiement');
