<?php

echo "🔍 Vérification Finale pour le Déploiement MAXITSA\n";
echo "=================================================\n\n";

$errors = [];
$warnings = [];

// 1. Vérification des fichiers de configuration
echo "📁 Vérification des fichiers de configuration...\n";

$requiredFiles = [
    'render.yaml' => 'Configuration Render',
    'Dockerfile' => 'Configuration Docker',
    'deploy.php' => 'Script de déploiement',
    '.env.production' => 'Variables d\'environnement production',
    'composer.json' => 'Dépendances PHP',
    'app/config/env.php' => 'Configuration environnement'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ {$description} : {$file}\n";
    } else {
        $errors[] = "❌ Fichier manquant : {$file} ({$description})";
    }
}

// 2. Vérification des migrations
echo "\n🗃 Vérification des migrations...\n";

$migrations = [
    'migrations/migration.php',
    'migrations/migration_woyofal.php',
    'migrations/migration_depot.php',
    'migrations/migration_enum_types.php'
];

foreach ($migrations as $migration) {
    if (file_exists(__DIR__ . '/' . $migration)) {
        echo "✅ Migration : {$migration}\n";
    } else {
        $warnings[] = "⚠️  Migration manquante : {$migration}";
    }
}

// 3. Vérification des dépendances Composer
echo "\n📦 Vérification de Composer...\n";

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✅ Dépendances Composer installées\n";
} else {
    $errors[] = "❌ Dépendances manquantes. Exécutez : composer install";
}

// 4. Vérification de la structure des dossiers
echo "\n📂 Vérification de la structure...\n";

$requiredDirs = [
    'app/core',
    'app/config', 
    'src/controller',
    'src/repository',
    'src/service',
    'templates',
    'public',
    'routes'
];

foreach ($requiredDirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "✅ Dossier : {$dir}\n";
    } else {
        $warnings[] = "⚠️  Dossier manquant : {$dir}";
    }
}

// 5. Vérification de la configuration Git
echo "\n🔄 Vérification Git...\n";

if (is_dir(__DIR__ . '/.git')) {
    echo "✅ Repository Git initialisé\n";
    
    // Vérifier s'il y a des changements non commitées
    $status = shell_exec('git status --porcelain 2>/dev/null');
    if (empty(trim($status))) {
        echo "✅ Tous les fichiers sont commitées\n";
    } else {
        $warnings[] = "⚠️  Il y a des changements non commitées";
    }
} else {
    $errors[] = "❌ Repository Git non initialisé";
}

// 6. Test de la configuration locale
echo "\n🔧 Test de configuration locale...\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/app/config/env.php';
    echo "✅ Configuration chargée avec succès\n";
} catch (Exception $e) {
    $errors[] = "❌ Erreur de configuration : " . $e->getMessage();
}

// 7. Rapport final
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 RAPPORT FINAL\n";
echo str_repeat("=", 50) . "\n";

if (empty($errors)) {
    echo "🎉 SUCCÈS : Prêt pour le déploiement !\n";
} else {
    echo "❌ ERREURS À CORRIGER :\n";
    foreach ($errors as $error) {
        echo "   {$error}\n";
    }
}

if (!empty($warnings)) {
    echo "\n⚠️  AVERTISSEMENTS :\n";
    foreach ($warnings as $warning) {
        echo "   {$warning}\n";
    }
}

echo "\n🚀 Étapes suivantes :\n";
echo "1. Corrigez les erreurs s'il y en a\n";
echo "2. Commitez et pushez sur GitHub\n";
echo "3. Créez les services sur Render\n";
echo "4. Suivez le guide dans GUIDE_DEPLOIEMENT.md\n";

echo "\n✨ Bonne chance avec votre déploiement !\n";
