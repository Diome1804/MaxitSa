<?php
/**
 * Debug spécifique pour l'API AppDAF depuis Render
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

// En-tête HTML
header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>Debug AppDAF</title></head><body><pre style='font-family: monospace; background: #f4f4f4; padding: 20px; border-radius: 8px;'>";

echo "=== DEBUG API APPDAF DEPUIS RENDER ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

echo "=== CONFIGURATION ===\n";
echo "APPDAF_API_URL: " . APPDAF_API_URL . "\n";
echo "APP_URL: " . APP_URL . "\n";
echo "RENDER: " . (getenv('RENDER') ?: 'Non définie') . "\n\n";

// Test 1: Ping de base de l'API
echo "=== TEST 1: PING API APPDAF ===\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => APPDAF_API_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_NOBODY => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
echo "Erreur: " . ($error ?: 'Aucune') . "\n";
echo "Statut: " . ($httpCode == 200 ? '✅ OK' : '❌ ERREUR') . "\n\n";

// Test 2: Test avec le service AppDAF complet
echo "=== TEST 2: SERVICE APPDAF ===\n";
try {
    $appDAFService = new \Src\Service\AppDAFService();
    echo "Service instancié: ✅\n";
    
    // Test disponibilité
    $isAvailable = $appDAFService->isAPIDisponible();
    echo "API disponible: " . ($isAvailable ? '✅ OUI' : '❌ NON') . "\n";
    
    // Test avec CNI connu
    $testCNI = '1234567890123';
    echo "Test avec CNI: $testCNI\n";
    
    $result = $appDAFService->rechercherCitoyenParCNI($testCNI);
    if ($result) {
        echo "✅ CNI trouvé:\n";
        echo "  Nom: " . ($result['nom'] ?? 'N/A') . "\n";
        echo "  Prénom: " . ($result['prenom'] ?? 'N/A') . "\n";
        echo "  Date naissance: " . ($result['dateNaissance'] ?? 'N/A') . "\n";
    } else {
        echo "❌ CNI non trouvé\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// Test 3: Test direct cURL comme dans le contrôleur
echo "\n=== TEST 3: SIMULATION CONTROLEUR ===\n";
$url = APPDAF_API_URL . '/api/citoyen/rechercher';
$data = ['nci' => '1234567890123'];
$jsonData = json_encode($data);

echo "URL: $url\n";
echo "Données: $jsonData\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $jsonData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
echo "Erreur cURL: " . ($error ?: 'Aucune') . "\n";
echo "Réponse: $result\n";

if ($result && $httpCode == 200) {
    $response = json_decode($result, true);
    if ($response) {
        echo "✅ Réponse décodée:\n";
        print_r($response);
    }
}

// Test 4: Simulation de la méthode verifierCNI du contrôleur
echo "\n=== TEST 4: SIMULATION VERIFIER-CNI ===\n";
try {
    // Simuler l'input JSON
    $input = ['nci' => '1234567890123'];
    echo "Input simulé: " . json_encode($input) . "\n";
    
    $appDAFService = new \Src\Service\AppDAFService();
    $citoyenData = $appDAFService->rechercherCitoyenParCNI(trim($input['nci']));
    
    if ($citoyenData !== null) {
        $response = [
            'statut' => 'success',
            'data' => $citoyenData
        ];
        echo "✅ Réponse du contrôleur: " . json_encode($response) . "\n";
    } else {
        $response = [
            'statut' => 'error',
            'message' => 'CNI non trouvé'
        ];
        echo "❌ Réponse du contrôleur: " . json_encode($response) . "\n";
    }
} catch (\Exception $e) {
    $response = [
        'statut' => 'error',
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ];
    echo "❌ Erreur contrôleur: " . json_encode($response) . "\n";
}

echo "\n=== FIN DEBUG ===\n";
echo "</pre></body></html>";
