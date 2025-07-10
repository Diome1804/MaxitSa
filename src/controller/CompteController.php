<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use App\Core\Database;
use App\Core\Validator;
use Src\Repository\UserRepository;

class CompteController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire d'inscription
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $num_carte_identite = $_POST['num_carte_identite'] ?? '';
            $telephone = $_POST['telephone'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validation des données
            $errors = [];
            
            if (empty($nom)) {
                $errors[] = "Le nom est requis";
            }
            
            if (empty($prenom)) {
                $errors[] = "Le prénom est requis";
            }
            
            if (empty($telephone)) {
                $errors[] = "Le numéro de téléphone est requis";
            }
            
            if (empty($num_carte_identite)) {
                $errors[] = "Le numéro de carte d'identité est requis";
            }
            
            if (!Validator::validatePassword($password)) {
                $errors = array_merge($errors, Validator::getErrors());
            }
            
            // Vérifier si le téléphone existe déjà
            if ($this->userRepository->findByTelephone($telephone)) {
                $errors[] = "Ce numéro de téléphone est déjà utilisé";
            }
            
            // Vérifier si le numéro de carte d'identité existe déjà
            if ($this->userRepository->findByNumCarteIdentite($num_carte_identite)) {
                $errors[] = "Ce numéro de carte d'identité est déjà utilisé";
            }
            
            // Gestion des fichiers uploadés
            $photoRecto = null;
            $photoVerso = null;
            
            if (isset($_FILES['photo_cni_recto']) && $_FILES['photo_cni_recto']['error'] === 0) {
                $photoRecto = $this->uploadFile($_FILES['photo_cni_recto'], 'recto');
            }
            
            if (isset($_FILES['photo_cni_verso']) && $_FILES['photo_cni_verso']['error'] === 0) {
                $photoVerso = $this->uploadFile($_FILES['photo_cni_verso'], 'verso');
            }
            
            // Si pas d'erreurs, sauvegarder en base
            if (empty($errors)) {
                try {
                    // Commencer une transaction
                    Database::getInstance()->beginTransaction();
                    
                    // 1. Créer l'utilisateur
                    $userData = [
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'adresse' => $adresse,
                        'num_carte_identite' => $num_carte_identite,
                        'photorecto' => $photoRecto,
                        'photoverso' => $photoVerso,
                        'telephone' => $telephone,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'type_id' => 1 // 1 = client par défaut
                    ];
                    
                    $userId = $this->userRepository->create($userData);
                    
                    if ($userId) {
                        // 2. Créer un compte principal pour l'utilisateur
                        $numCompte = $this->generateNumCompte();
                        $compteData = [
                            'num_compte' => $numCompte,
                            'solde' => 0.00,
                            'user_id' => $userId,
                            'type' => 'ComptePrincipal',
                            'num_telephone' => $telephone
                        ];
                        
                        $compteId = $this->userRepository->createCompte($compteData);
                        
                        if ($compteId) {
                            // Valider la transaction
                            Database::getInstance()->commit();
                            
                            // Inscription réussie, créer la session et rediriger
                            session_start();
                            $_SESSION['user_id'] = $userId;
                            $_SESSION['user_telephone'] = $telephone;
                            $_SESSION['user_nom'] = $nom . ' ' . $prenom;
                            $_SESSION['compte_id'] = $compteId;
                            $_SESSION['num_compte'] = $numCompte;
                            
                            header('Location: /dashboard');
                            exit;
                        } else {
                            throw new \Exception("Erreur lors de la création du compte");
                        }
                    } else {
                        throw new \Exception("Erreur lors de la création de l'utilisateur");
                    }
                    
                } catch (\Exception $e) {
                    // Annuler la transaction en cas d'erreur
                    Database::getInstance()->rollback();
                    $errors[] = "Erreur lors de la création du compte: " . $e->getMessage();
                }
            }
            
            // Si erreurs, retourner au formulaire avec les erreurs
            $this->renderHtmlLogin('login/inscription.html.php', ['errors' => $errors]);
        }
    }
    
    public function index()
{
    // ✅ Vérification manuelle de l'authentification
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    // Récupérer les données de l'utilisateur et du compte
    $userId = $_SESSION['user_id'];
    
    try {
        $user = $this->userRepository->findById($userId);
        $compte = $this->userRepository->getCompteByUserId($userId);
        $transactions = $this->userRepository->getTransactionsByUserId($userId);
    } catch (\Exception $e) {
        // En cas d'erreur, initialiser avec des valeurs par défaut
        $user = ['nom' => 'Utilisateur', 'prenom' => ''];
        $compte = ['num_compte' => 'N/A', 'type' => 'N/A', 'solde' => 0, 'num_telephone' => 'N/A'];
        $transactions = [];
    }

    $data = [
        'user' => $user,
        'compte' => $compte,
        'transactions' => $transactions
    ];

    // Rendre le template dashboard
    $this->render('dashboard/dashboard.html.php', $data);
}

    
    private function uploadFile($file, $prefix)
    {
        $uploadDir = 'uploads/cni/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = $prefix . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $fileName; // Stocker seulement le nom du fichier
        }
        
        return null;
    }
    
    private function generateNumCompte(): string
    {
        return 'CPT' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function destroy() {}
    public function show() {}
    public function edit() {}
    public function update() {}
    public function create() {}



    


}
