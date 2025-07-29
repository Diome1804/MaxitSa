<?php
/**
 * Script pour mettre à jour les types de transaction existants
 * Pour correspondre aux nouveaux enums PostgreSQL
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/env.php';

try {
    $pdo = new PDO(dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "=== MISE À JOUR DES TYPES DE TRANSACTION ===\n";
    
    // Mapping des anciens types vers les nouveaux
    $typeMapping = [
        'depot' => 'Depot',
        'retrait' => 'Retrait',
        'transfert' => 'Transfert',
        'transfert_sortant' => 'Transfert',
        'transfert_entrant' => 'Transfert',
        'frais_transfert' => 'Retrait',
        'paiement' => 'Woyofal',
        'woyofal' => 'Woyofal',
        'annulation_depot' => 'Retrait'
    ];
    
    // Vérifier les types existants
    $checkTypes = $pdo->query("SELECT DISTINCT type FROM transactions WHERE type IS NOT NULL");
    $existingTypes = $checkTypes->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Types existants trouvés: " . implode(', ', $existingTypes) . "\n\n";
    
    $pdo->beginTransaction();
    
    foreach ($typeMapping as $oldType => $newType) {
        echo "Mise à jour: '$oldType' → '$newType'\n";
        $stmt = $pdo->prepare("UPDATE transactions SET type = :new_type WHERE type = :old_type");
        $stmt->execute([':new_type' => $newType, ':old_type' => $oldType]);
        $affected = $stmt->rowCount();
        if ($affected > 0) {
            echo "  → $affected transaction(s) mise(s) à jour\n";
        }
    }
    
    $pdo->commit();
    
    // Vérification finale
    echo "\n=== VÉRIFICATION FINALE ===\n";
    $finalCheck = $pdo->query("SELECT type, COUNT(*) as count FROM transactions GROUP BY type ORDER BY type");
    $finalTypes = $finalCheck->fetchAll();
    
    foreach ($finalTypes as $typeInfo) {
        echo "Type '{$typeInfo['type']}': {$typeInfo['count']} transaction(s)\n";
    }
    
    echo "\n✅ Mise à jour terminée avec succès !\n";
    
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
