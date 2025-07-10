<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Exception;

class CompteController extends AbstractController
{
    public function __construct()
    {
        // Initialisation si nécessaire
    }

    /**
     * Afficher le dashboard
     */
    public function index()
    {
        $data = [
            'success_message' => $_GET['success_message'] ?? null,
            'error_message' => $_GET['error_message'] ?? null,
            'user_numero' => $_GET['user_numero'] ?? 'Utilisateur',
            'user' => [
                'nom' => 'Utilisateur',
                'prenom' => 'Test',
                'solde' => '13.000 FCFA',
                'numero' => $_GET['user_numero'] ?? '123456789'
            ]
        ];

        $this->renderHtml('dashbord/dashboard.html.php', $data);
    }

    /**
     * Traiter l'inscription
     */
    public function store()
    {
        try {
            error_log("CompteController::store() - Traitement inscription");
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            // Récupérer les données
            $userData = [
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'telephone' => trim($_POST['telephone'] ?? ''),
                'adresse' => trim($_POST['adresse'] ?? ''),
                'numero_cni' => trim($_POST['numero_cni'] ?? ''),
                'password' => $_POST['password'] ?? ''
            ];

            error_log("Données inscription: " . print_r($userData, true));

            // Validation
            $errors = [];
            if (empty($userData['nom'])) $errors[] = 'Le nom est obligatoire';
            if (empty($userData['prenom'])) $errors[] = 'Le prénom est obligatoire';
            if (empty($userData['telephone'])) $errors[] = 'Le téléphone est obligatoire';
            if (empty($userData['adresse'])) $errors[] = 'L\'adresse est obligatoire';
            if (empty($userData['numero_cni'])) $errors[] = 'Le numéro CNI est obligatoire';
            if (empty($userData['password'])) $errors[] = 'Le mot de passe est obligatoire';

            if (!empty($errors)) {
                throw new Exception(implode(', ', $errors));
            }

            // Validation des fichiers (simplifiée)
            if (!isset($_FILES['photo_cni_recto']) || $_FILES['photo_cni_recto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Photo CNI recto obligatoire');
            }
            if (!isset($_FILES['photo_cni_verso']) || $_FILES['photo_cni_verso']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Photo CNI verso obligatoire');
            }

            // Simuler la création du compte
            $numCompte = 'CPT' . date('Y') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            
            error_log("Inscription réussie - Compte: {$numCompte}, Nom: {$userData['nom']} {$userData['prenom']}");

            // REDIRECTION DIRECTE VERS DASHBOARD APRÈS INSCRIPTION
            $message = urlencode("Inscription réussie ! Bienvenue {$userData['prenom']} ! Votre numéro de compte: {$numCompte}");
            $userNumero = urlencode($userData['telephone']); // Utiliser le téléphone comme identifiant
            
            header("Location: /dashboard?success_message={$message}&user_numero={$userNumero}");
            exit;

        } catch (Exception $e) {
            error_log("Erreur inscription: " . $e->getMessage());
            $errorMessage = urlencode($e->getMessage());
            header("Location: /register?error_message={$errorMessage}");
            exit;
        }
    }

    /**
     * Méthodes abstraites obligatoires
     */
    public function show() 
    {
        // header("Location: /dashboard");
        // exit;
    }

    public function create() 
    {
        // header("Location: /register");
        // exit;
    }

    public function edit() 
    {
        // header("Location: /dashboard");
        // exit;
    }

    public function destroy() 
    {
        // $message = urlencode('Déconnexion réussie');
        // header("Location: /login?success_message={$message}");
        // exit;
    }

    /**
     * Méthode pour rendre les vues
     */
    protected function renderHtml(string $template, array $data = []): void
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Construire le chemin du template
        $templatePath = __DIR__ . '/../../templates/' . $template;
        
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            echo "<h1>Template introuvable: {$template}</h1>";
            echo "<p>Chemin: {$templatePath}</p>";
        }
    }

    
}
