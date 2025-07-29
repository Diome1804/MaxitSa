<?php

namespace Src\Service;

use App\Core\Interfaces\AppDAFServiceInterface;

class AppDAFService implements AppDAFServiceInterface
{
    private string $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = APPDAF_API_URL;
    }
    
    /**
     * Recherche un citoyen par son numéro CNI dans l'API AppDAF
     * 
     * @param string $nci Numéro de carte d'identité (13 chiffres)
     * @return array|null Données du citoyen ou null si non trouvé
     * @throws \Exception En cas d'erreur de communication avec l'API
     */
    public function rechercherCitoyenParCNI(string $nci): ?array 
    {
        // Validation du format CNI
        if (!preg_match('/^[0-9]{13}$/', $nci)) {
            throw new \Exception('Le numéro CNI doit contenir exactement 13 chiffres');
        }
        
        $url = $this->baseUrl . '/api/citoyen/rechercher';
        
        $data = [
            'nci' => $nci
        ];
        
        $jsonData = json_encode($data);
        
        // Log pour debug en production
        error_log("AppDAF: Tentative de recherche CNI - URL: $url - CNI: $nci");
        
        // Utilisation de cURL avec configuration renforcée
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60, // Augmenté à 60 secondes
            CURLOPT_CONNECTTIMEOUT => 30, // Timeout de connexion
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData),
                'User-Agent: MaxitSA-App/1.0',
                'Accept: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);
        
        try {
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $curlInfo = curl_getinfo($ch);
            
            // Log détaillé pour debug
            error_log("AppDAF Response - HTTP: $httpCode - Error: " . ($error ?: 'none') . " - Time: " . $curlInfo['total_time']);
            
            curl_close($ch);
            
            if ($result === false) {
                $errorMsg = $error ?: 'Erreur cURL inconnue';
                error_log("AppDAF: Erreur cURL - $errorMsg");
                throw new \Exception("Erreur de connexion à l'API AppDAF: $errorMsg");
            }
            
            if (!empty($error)) {
                error_log("AppDAF: Erreur cURL - $error");
                throw new \Exception("Erreur de communication avec l'API AppDAF: $error");
            }
            
            if ($httpCode !== 200) {
                error_log("AppDAF: HTTP Error $httpCode - Response: " . substr($result, 0, 500));
                throw new \Exception("Erreur serveur API AppDAF (HTTP $httpCode)");
            }
            
            $responseData = json_decode($result, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $jsonError = json_last_error_msg();
                error_log("AppDAF: JSON Error - $jsonError - Response: " . substr($result, 0, 500));
                throw new \Exception("Réponse invalide de l'API AppDAF: $jsonError");
            }
            
            error_log("AppDAF: Réponse décodée - " . json_encode($responseData));
            
            // Vérification du statut de la réponse
            if (isset($responseData['statut']) && $responseData['statut'] === 'success') {
                return $responseData['data'] ?? null;
            } elseif (isset($responseData['statut']) && $responseData['statut'] === 'error') {
                // CNI non trouvé dans la base AppDAF
                return null;
            } else {
                throw new \Exception('Format de réponse inattendu de l\'API AppDAF');
            }
            
        } catch (\Exception $e) {
            if (isset($ch)) {
                curl_close($ch);
            }
            error_log("AppDAF: Exception - " . $e->getMessage());
            throw new \Exception('Service AppDAF temporairement indisponible: ' . $e->getMessage());
        }
    }
    
    /**
     * Vérifie si l'API AppDAF est disponible
     * 
     * @return bool
     */
    public function isAPIDisponible(): bool 
    {
        try {
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->baseUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_HTTPHEADER => [
                    'User-Agent: MaxitSA-App/1.0'
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_NOBODY => true // HEAD request
            ]);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);
            
            // API disponible si pas d'erreur cURL et code HTTP acceptable
            return $result !== false && empty($error) && $httpCode >= 200 && $httpCode < 400;
            
        } catch (\Exception $e) {
            error_log("AppDAF: Erreur test disponibilité - " . $e->getMessage());
            return false;
        }
    }
}
