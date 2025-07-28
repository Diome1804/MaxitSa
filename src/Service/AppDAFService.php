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
        
        // Utilisation de cURL au lieu de file_get_contents
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        try {
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);
            
            if ($result === false || !empty($error)) {
                throw new \Exception('Erreur cURL: ' . $error);
            }
            
            if ($httpCode !== 200) {
                throw new \Exception('HTTP Error: ' . $httpCode);
            }
            
            $responseData = json_decode($result, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Réponse JSON invalide de l\'API AppDAF');
            }
            
            // Vérification du statut de la réponse
            if (isset($responseData['statut']) && $responseData['statut'] === 'success') {
                return $responseData['data'];
            } elseif (isset($responseData['statut']) && $responseData['statut'] === 'error') {
                // CNI non trouvé
                return null;
            } else {
                throw new \Exception('Format de réponse inattendu de l\'API AppDAF');
            }
            
        } catch (\Exception $e) {
            if (isset($ch)) {
                curl_close($ch);
            }
            throw new \Exception('Erreur lors de la recherche CNI: ' . $e->getMessage());
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
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5
                ]
            ]);
            
            $result = file_get_contents($this->baseUrl, false, $context);
            return $result !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
