<?php

// Script pour exécuter le seeder avec la base de données Railway
$_ENV['DATABASE_URL'] = 'postgresql://postgres:IoiQfHDMYkFAXvwkHaawDOlpkKJPLslx@shuttle.proxy.rlwy.net:30832/railway';
putenv('DATABASE_URL=postgresql://postgres:IoiQfHDMYkFAXvwkHaawDOlpkKJPLslx@shuttle.proxy.rlwy.net:30832/railway');

echo "Connexion à la base de données Railway...\n";
echo "DATABASE_URL définie: " . getenv('DATABASE_URL') . "\n\n";

// Exécuter le seeder
require_once __DIR__ . '/seeders/seeder.php';
