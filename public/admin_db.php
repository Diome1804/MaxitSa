<?php
// Page d'administration simple pour voir les données
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

$password_check = $_GET['admin'] ?? '';
if ($password_check !== 'Dakar2026') {
    die('Accès refusé. Ajoutez ?admin=Dakar2026 à l\'URL');
}

try {
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>MAXITSA - Administration DB</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section { margin: 30px 0; }
        h2 { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 5px; }
    </style>
</head>
<body>
    <h1>🛠 MAXITSA - Administration Base de Données</h1>
    
    <div class="section">
        <h2>👥 Utilisateurs</h2>
        <?php
        try {
            $stmt = $pdo->query('SELECT id, nom, prenom, telephone, email, type_user_id FROM "user" ORDER BY id');
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($users) {
                echo "<table><tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Téléphone</th><th>Email</th><th>Type</th></tr>";
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>{$user['id']}</td>";
                    echo "<td>{$user['nom']}</td>";
                    echo "<td>{$user['prenom']}</td>";
                    echo "<td>{$user['telephone']}</td>";
                    echo "<td>{$user['email']}</td>";
                    echo "<td>{$user['type_user_id']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Aucun utilisateur trouvé.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Erreur : " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>💳 Comptes</h2>
        <?php
        try {
            $stmt = $pdo->query('SELECT c.id, c.num_compte, c.solde, u.nom, u.prenom FROM compte c JOIN "user" u ON c.user_id = u.id ORDER BY c.id');
            $comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($comptes) {
                echo "<table><tr><th>ID</th><th>Numéro</th><th>Solde</th><th>Propriétaire</th></tr>";
                foreach ($comptes as $compte) {
                    echo "<tr>";
                    echo "<td>{$compte['id']}</td>";
                    echo "<td>{$compte['num_compte']}</td>";
                    echo "<td>{$compte['solde']} FCFA</td>";
                    echo "<td>{$compte['nom']} {$compte['prenom']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Aucun compte trouvé.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Erreur : " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>💰 Transactions (10 dernières)</h2>
        <?php
        try {
            $stmt = $pdo->query('SELECT t.id, t.type, t.montant, t.date, t.statut, c.num_compte FROM transactions t LEFT JOIN compte c ON t.compte_id = c.id ORDER BY t.date DESC LIMIT 10');
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($transactions) {
                echo "<table><tr><th>ID</th><th>Type</th><th>Montant</th><th>Date</th><th>Statut</th><th>Compte</th></tr>";
                foreach ($transactions as $transaction) {
                    echo "<tr>";
                    echo "<td>{$transaction['id']}</td>";
                    echo "<td>{$transaction['type']}</td>";
                    echo "<td>{$transaction['montant']} FCFA</td>";
                    echo "<td>{$transaction['date']}</td>";
                    echo "<td>{$transaction['statut']}</td>";
                    echo "<td>{$transaction['num_compte']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Aucune transaction trouvée.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Erreur : " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>📊 Statistiques</h2>
        <?php
        try {
            $stmt = $pdo->query('SELECT COUNT(*) as total FROM "user"');
            $userCount = $stmt->fetchColumn();
            
            $stmt = $pdo->query('SELECT COUNT(*) as total FROM compte');
            $compteCount = $stmt->fetchColumn();
            
            $stmt = $pdo->query('SELECT COUNT(*) as total FROM transactions');
            $transactionCount = $stmt->fetchColumn();
            
            echo "<ul>";
            echo "<li><strong>Utilisateurs :</strong> {$userCount}</li>";
            echo "<li><strong>Comptes :</strong> {$compteCount}</li>";
            echo "<li><strong>Transactions :</strong> {$transactionCount}</li>";
            echo "</ul>";
        } catch (Exception $e) {
            echo "<p>Erreur : " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <p><small>🔒 Interface d'administration sécurisée - MAXITSA Production</small></p>
</body>
</html>
