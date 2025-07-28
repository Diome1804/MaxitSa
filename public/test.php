<?php
// Test pour déboguer les problèmes sur Render

echo "<h1>Test Debug - Render</h1>";
echo "<h2>1. Informations PHP</h2>";
echo "Version PHP: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

echo "<h2>2. Variables d'environnement</h2>";
echo "APP_URL: " . (getenv('APP_URL') ?: 'Non définie') . "<br>";
echo "DATABASE_URL: " . (getenv('DATABASE_URL') ? 'Définie (masquée)' : 'Non définie') . "<br>";
echo "RENDER: " . (getenv('RENDER') ?: 'Non définie') . "<br>";

echo "<h2>3. Test de connexion à la base</h2>";
try {
    require_once __DIR__ . "/../vendor/autoload.php";
    require_once __DIR__ . "/../app/config/env.php";
    
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD);
    echo "✅ Connexion à la base de données réussie<br>";
    
    // Test simple d'un utilisateur
    $stmt = $pdo->prepare("SELECT telephone, nom, prenom FROM \"user\" LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch();
    if ($user) {
        echo "✅ Utilisateur test trouvé: " . $user['prenom'] . " " . $user['nom'] . " (" . $user['telephone'] . ")<br>";
    } else {
        echo "❌ Aucun utilisateur trouvé<br>";
    }
    
    // Test compte principal
    $stmt = $pdo->prepare("SELECT num_compte, solde, type FROM compte WHERE type = 'ComptePrincipal' LIMIT 1");
    $stmt->execute();
    $compte = $stmt->fetch();
    if ($compte) {
        echo "✅ Compte principal trouvé: " . $compte['num_compte'] . " - Solde: " . number_format($compte['solde']) . " FCFA<br>";
    } else {
        echo "❌ Aucun compte principal trouvé<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "<br>";
}

echo "<h2>4. Test du routage</h2>";
echo "<a href='/dashboard'>Test lien dashboard</a><br>";
echo "<a href='/woyofal'>Test lien woyofal</a><br>";
echo "<a href='/transactions'>Test lien transactions</a><br>";

echo "<h2>5. Fichiers présents</h2>";
echo "index.php: " . (file_exists(__DIR__ . '/index.php') ? '✅' : '❌') . "<br>";
echo "router.php: " . (file_exists(__DIR__ . '/router.php') ? '✅' : '❌') . "<br>";
echo ".htaccess: " . (file_exists(__DIR__ . '/.htaccess') ? '✅' : '❌') . "<br>";
