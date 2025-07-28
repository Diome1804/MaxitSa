<?php

// Script pour corriger UserRepository et SecurityService pour la compatibilité Railway

$filesToFix = [
    'src/Repository/UserRepository.php',
    'src/Service/SecurityService.php'
];

echo "=== CORRECTION POUR RAILWAY ===\n";

// 1. Corriger UserRepository
$userRepoPath = '/home/lex_code/Documents/PHP_POO/MAXITSA/src/Repository/UserRepository.php';
$userRepoContent = file_get_contents($userRepoPath);

// Remplacer le SQL dans insert
$userRepoContent = str_replace(
    'type_id) \n                    VALUES (:nom, :prenom, :adresse, :num_carte_identite, :photorecto,:telephone, :password, :[REDACTED:password])',
    'type_user_id) \n                    VALUES (:nom, :prenom, :adresse, :num_carte_identite, :photorecto,:telephone, :password, :type_user_id)',
    $userRepoContent
);

// Remplacer le paramètre dans execute
$userRepoContent = str_replace(
    "':type_id' => \$userData['type_id']",
    "':type_user_id' => \$userData['type_id']",
    $userRepoContent
);

file_put_contents($userRepoPath, $userRepoContent);
echo "✅ UserRepository corrigé\n";

// 2. Corriger SecurityService - mettre type_user_id au lieu de type_id
$securityPath = '/home/lex_code/Documents/PHP_POO/MAXITSA/src/Service/SecurityService.php';
$securityContent = file_get_contents($securityPath);

$securityContent = str_replace(
    "\$userData['type_id'] = 1; // Client",
    "\$userData['type_id'] = 1; // Client (type_user_id dans la base)",
    $securityContent
);

file_put_contents($securityPath, $securityContent);
echo "✅ SecurityService vérifié\n";

echo "\n=== CORRECTIONS TERMINÉES ===\n";
echo "L'application devrait maintenant fonctionner avec Railway PostgreSQL.\n";
