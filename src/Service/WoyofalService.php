<?php

namespace Src\Service;

use App\Core\Interfaces\WoyofalServiceInterface;
use App\Core\Interfaces\CompteServiceInterface;
use Src\Repository\TransactionRepository;
use App\Core\ReflectionFactory;
use App\Core\Lang;

class WoyofalService implements WoyofalServiceInterface
{
    private string $baseUrl;
    private int $timeout = 30;
    private int $maxRetries = 3;
    
    private CompteServiceInterface $compteService;
    private TransactionRepository $transactionRepository;
    private ReflectionFactory $factory;

    public function __construct(
        CompteServiceInterface $compteService,
        TransactionRepository $transactionRepository,
        ReflectionFactory $factory
    ) {
        $this->baseUrl = WOYOFAL_API_URL;
        $this->compteService = $compteService;
        $this->transactionRepository = $transactionRepository;
        $this->factory = $factory;
    }

    public function acheterCode(string $compteur, float $montant, int $userId): array
    {
        try {
            // Étape 1 : Validation des données
            $erreurs = $this->validerDonneesAchat($compteur, $montant);
            if (!empty($erreurs)) {
                return [
                    'success' => false,
                    'message' => implode(', ', $erreurs),
                    'errors' => $erreurs
                ];
            }

            // Étape 2 : Vérification du solde
            if (!$this->verifierSoldeDisponible($userId, $montant)) {
                return [
                    'success' => false,
                    'message' => Lang::get('woyofal.insufficient_balance'),
                    'errors' => ['solde' => 'Solde insuffisant']
                ];
            }

            // Étape 3 : Appel API Woyofal avec retry
            $woyofalResponse = $this->appellerAPIWoyofal($compteur, $montant);
            
            if (!$woyofalResponse['success']) {
                return $woyofalResponse;
            }

            // Étape 4 : Débiter le compte principal
            $debitResult = $this->debiterCompte($userId, $montant);
            if (!$debitResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du débit du compte',
                    'errors' => ['debit' => $debitResult['message']]
                ];
            }

            // Étape 5 : Enregistrer la transaction
            $transactionId = $this->enregistrerTransaction($userId, $montant, $woyofalResponse['data']);
            
            // Étape 6 : Générer le reçu
            $recu = $this->genererRecu([
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'montant' => $montant
            ], $woyofalResponse['data']);

            return [
                'success' => true,
                'message' => 'Achat effectué avec succès',
                'data' => $woyofalResponse['data'],
                'recu' => $recu,
                'transaction_id' => $transactionId
            ];

        } catch (\Exception $e) {
            error_log("Erreur achat Woyofal: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur technique lors de l\'achat',
                'errors' => ['system' => $e->getMessage()]
            ];
        }
    }

    public function validerDonneesAchat(string $compteur, float $montant): array
    {
        $erreurs = [];

        // Validation du compteur
        if (empty($compteur)) {
            $erreurs['compteur'] = 'Le numéro de compteur est obligatoire';
        } elseif (!preg_match('/^[0-9]{9,11}$/', $compteur)) {
            $erreurs['compteur'] = 'Le numéro de compteur doit contenir entre 9 et 11 chiffres';
        }

        // Validation du montant
        if ($montant <= 0) {
            $erreurs['montant'] = 'Le montant doit être supérieur à 0';
        } elseif ($montant < 500) {
            $erreurs['montant'] = 'Le montant minimum est de 500 FCFA';
        } elseif ($montant > 500000) {
            $erreurs['montant'] = 'Le montant maximum est de 500 000 FCFA';
        }

        return $erreurs;
    }

    public function verifierSoldeDisponible(int $userId, float $montant): bool
    {
        try {
            $soldeActuel = $this->compteService->getSolde($userId);
            return $soldeActuel >= $montant;
        } catch (\Exception $e) {
            error_log("Erreur vérification solde: " . $e->getMessage());
            return false;
        }
    }

    public function genererRecu(array $transactionData, array $woyofalResponse): array
    {
        return [
            'transaction_id' => $transactionData['transaction_id'],
            'client_name' => $woyofalResponse['client'] ?? 'Client Woyofal',
            'compteur' => $woyofalResponse['compteur'],
            'code_recharge' => $woyofalResponse['code'],
            'nombre_kwh' => $woyofalResponse['nbreKwt'],
            'date_heure' => $woyofalResponse['date'],
            'tranche' => $woyofalResponse['tranche'],
            'prix_unitaire' => $woyofalResponse['prix'],
            'reference' => $woyofalResponse['reference'],
            'montant' => $transactionData['montant'],
            'date_achat' => date('Y-m-d H:i:s')
        ];
    }

    public function enregistrerTransaction(int $userId, float $montant, array $woyofalData): int|false
    {
        try {
            $transactionData = [
                'user_id' => $userId,
                'type' => 'woyofal',
                'montant' => $montant,
                'date' => date('Y-m-d H:i:s'), // Ajouter la date obligatoire
                'reference' => $woyofalData['reference'] ?? 'WYF' . uniqid(),
                'statut' => 'success',
                'details' => json_encode([
                    'compteur' => $woyofalData['compteur'],
                    'code' => $woyofalData['code'],
                    'client' => $woyofalData['client'],
                    'nbreKwt' => $woyofalData['nbreKwt'],
                    'tranche' => $woyofalData['tranche'],
                    'prix' => $woyofalData['prix']
                ]),
                'date_creation' => date('Y-m-d H:i:s')
            ];

            return $this->transactionRepository->insert($transactionData);
        } catch (\Exception $e) {
            error_log("Erreur enregistrement transaction: " . $e->getMessage());
            return false;
        }
    }

    public function getHistoriqueAchats(int $userId, int $limit = 10): array
    {
        try {
            // Cette méthode devra être implémentée dans TransactionRepository
            $transactions = $this->transactionRepository->findByUserIdAndType($userId, 'woyofal', $limit);
            
            $historique = [];
            foreach ($transactions as $transaction) {
                $details = json_decode($transaction['details'], true);
                $historique[] = [
                    'id' => $transaction['id'],
                    'date' => $transaction['date_creation'],
                    'montant' => $transaction['montant'],
                    'reference' => $transaction['reference'],
                    'compteur' => $details['compteur'] ?? '',
                    'client' => $details['client'] ?? '',
                    'code' => $details['code'] ?? '',
                    'statut' => $transaction['statut']
                ];
            }

            return $historique;
        } catch (\Exception $e) {
            error_log("Erreur historique achats: " . $e->getMessage());
            return [];
        }
    }

    public function getRecuParTransaction(int $transactionId, int $userId): ?array
    {
        try {
            $transaction = $this->transactionRepository->findByIdAndUserId($transactionId, $userId, 'woyofal');
            
            if (!$transaction) {
                return null;
            }

            $details = json_decode($transaction['details'], true);
            
            return [
                'transaction_id' => $transaction['id'],
                'client_name' => $details['client'] ?? 'Client Woyofal',
                'compteur' => $details['compteur'] ?? '',
                'code_recharge' => $details['code'] ?? '',
                'nombre_kwh' => $details['nbreKwt'] ?? 0,
                'date_heure' => $transaction['date_creation'],
                'tranche' => $details['tranche'] ?? '',
                'prix_unitaire' => $details['prix'] ?? 0,
                'reference' => $transaction['reference'],
                'montant' => $transaction['montant'],
                'date_achat' => $transaction['date_creation']
            ];
        } catch (\Exception $e) {
            error_log("Erreur récupération reçu: " . $e->getMessage());
            return null;
        }
    }

    private function appellerAPIWoyofal(string $compteur, float $montant): array
    {
        // TEMPORAIRE : Simulation de l'API pour les tests
        // TODO: Remplacer par l'appel API réel quand l'API sera disponible
        
        // Simuler une réponse de succès avec des données de test
        $simulatedResponse = [
            'success' => true,
            'data' => [
                'compteur' => $compteur,
                'code' => 'WYF' . rand(10000000, 99999999), // Code de recharge simulé
                'client' => 'Client Test Woyofal',
                'nbreKwt' => round($montant / 102, 2), // Simulation basée sur tranche normale
                'tranche' => 'Tranche 2 - Normal',
                'prix' => 102,
                'reference' => 'WYF' . date('YmdHis') . rand(100, 999),
                'date' => date('Y-m-d H:i:s')
            ],
            'message' => 'Achat effectué avec succès'
        ];
        
        return $simulatedResponse;
        
        /* CODE API RÉEL (à décommenter quand l'API sera prête) :
        $url = $this->baseUrl . '/api/woyofal/achat';
        $data = [
            'compteur' => $compteur,
            'montant' => $montant
        ];

        for ($retry = 0; $retry < $this->maxRetries; $retry++) {
            try {
                $ch = curl_init();
                
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => $this->timeout,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen(json_encode($data))
                    ],
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ]);
                
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                
                curl_close($ch);
                
                if ($result === false || !empty($error)) {
                    throw new \Exception('Erreur cURL: ' . $error);
                }
                
                $responseData = json_decode($result, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Réponse JSON invalide de l\'API Woyofal');
                }
                
                if ($httpCode === 200 && isset($responseData['statut']) && $responseData['statut'] === 'success') {
                    return [
                        'success' => true,
                        'data' => $responseData['data'],
                        'message' => $responseData['message']
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $responseData['message'] ?? 'Erreur API Woyofal',
                        'code' => $httpCode
                    ];
                }
                
            } catch (\Exception $e) {
                error_log("Tentative $retry failed: " . $e->getMessage());
                
                if ($retry === $this->maxRetries - 1) {
                    return [
                        'success' => false,
                        'message' => 'Service Woyofal temporairement indisponible',
                        'error' => $e->getMessage()
                    ];
                }
                
                // Backoff exponentiel
                sleep(pow(2, $retry));
            }
        }

        return [
            'success' => false,
            'message' => 'Échec de connexion à l\'API Woyofal'
        ];
        */
    }

    private function debiterCompte(int $userId, float $montant): array
    {
        try {
            // Utiliser le service CompteService pour débiter
            $soldeActuel = $this->compteService->getSolde($userId);
            
            if ($soldeActuel < $montant) {
                return [
                    'success' => false,
                    'message' => 'Solde insuffisant'
                ];
            }

            $nouveauSolde = $soldeActuel - $montant;
            
            // Débiter le compte
            $success = $this->compteService->updateSolde($userId, $nouveauSolde);
            
            if ($success) {
                return [
                    'success' => true,
                    'nouveau_solde' => $nouveauSolde
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors du débit du compte'
                ];
            }
            
        } catch (\Exception $e) {
            error_log("Erreur débit compte: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
