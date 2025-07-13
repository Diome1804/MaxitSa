<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\SecurityService;
use Src\Entity\User;
use Src\Repository\UsersRepository;
use Src\Repository\CompteRepository;
use App\Core\FileUpload;
use App\Core\Validator;
use App\Core\Session;
use App\Core\App;
use Src\Controller\CompteController;

class SecurityController extends AbstractController
{
    private SecurityService $securityService;
    private CompteRepository $compteRepository;
    private Validator $validator;
    private FileUpload $fileUpload;

    public function __construct()
    {
        parent::__construct();
        $this->compteRepository = App::getDependency('repository', 'compteRepo');
        $this->securityService = App::getDependency('services', 'securityServ');
        $this->validator = Validator::getInstance();
        $this->fileUpload = FileUpload::getInstance();
    }
    public function index()
{
   
    if (isset($_SESSION['user'])) {
        header("Location: /dashboard");
        exit();
    }
    // ✅ HEADERS ANTI-CACHE
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log("=== TENTATIVE DE CONNEXION ===");
        error_log("Numéro: " . ($_POST['numero'] ?? 'non fourni'));
        
        $user = $this->securityService->authenticate($_POST);
        
        if ($user) {
            $_SESSION['user'] = $user->toArray();

        // Rediriger vers la page de tableau de bord
             //$this->render('dashboard/dashboard.html.php');
            header("Location: /dashboard");
            
        }
    }

    $this->render("login/login.html.php", [
        'success' => $_SESSION['success'] ?? null,
        'old' => $_POST ?? [],
        'errors' => $_SESSION['errors'] ?? [],
    ]);

    unset($_SESSION['success'], $_SESSION['errors']);
}


    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Debug
            error_log("POST Data reçu: " . print_r($_POST, true));
            error_log("FILES Data reçu: " . print_r($_FILES, true));

            // UTILISATION DE VOTRE CLASSE VALIDATOR
            $rules = [
                'nom' => ['required', ['minLength', 2, 'Le nom doit contenir au moins 2 caractères']],
                'prenom' => ['required', ['minLength', 2, 'Le prénom doit contenir au moins 2 caractères']],
                'telephone' => ['required', ['isSenegalPhone', 'Format de téléphone sénégalais invalide']],
                'password' => ['required', ['isPassword', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial']],
                'adresse' => ['required', ['minLength', 5, 'L\'adresse doit contenir au moins 5 caractères']],
                'num_carte_identite' => ['required', ['isCNI', 'Le numéro de CNI doit commencer par 1 et contenir 13 chiffres']]
            ];

            // Validation des données POST
            if ($this->validator->validate($_POST, $rules)) {
                error_log("Validation réussie, tentative de création d'utilisateur...");

                try {
                    // Upload simple des photos
                    $photorectoUrl = '';
                    $photoversoUrl = '';

                    // Upload photo recto (optionnel pour test)
                    if (isset($_FILES['photorecto']) && $_FILES['photorecto']['error'] === UPLOAD_ERR_OK) {
                        $photorectoUrl = $this->uploadSimple($_FILES['photorecto'], 'recto');
                    }

                    // Upload photo verso (optionnel pour test)
                    if (isset($_FILES['photoverso']) && $_FILES['photoverso']['error'] === UPLOAD_ERR_OK) {
                        $photoversoUrl = $this->uploadSimple($_FILES['photoverso'], 'verso');
                    }

                    // Préparer les données utilisateur
                    $userData = [
                        'nom' => trim($_POST['nom']),
                        'prenom' => trim($_POST['prenom']),
                        'adresse' => trim($_POST['adresse']),
                        'num_carte_identite' => trim($_POST['num_carte_identite']),
                        'telephone' => trim($_POST['telephone']),
                        'password' => $_POST['password'],
                        'photorecto' => $photorectoUrl,
                        'photoverso' => $photoversoUrl,
                    ];

                    error_log("UserData préparé: " . print_r($userData, true));

                    // Créer le client avec compte principal
                    $userId = $this->securityService->creerClientAvecComptePrincipal($userData, 0.0);

                    if ($userId !== false) {
                        error_log("Inscription réussie, redirection vers /");
                        Session::set('success', 'Inscription réussie ! Votre compte principal a été créé.');
                        $this->redirect('/');
                        exit;
                    } else {
                        error_log("Échec de l'inscription");
                        Session::set('errors', ['general' => 'Erreur lors de l\'inscription. Veuillez réessayer.']);
                    }

                } catch (\Exception $e) {
                    error_log("Exception lors de l'inscription: " . $e->getMessage());
                    Session::set('errors', ['general' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
                }
            } else {
                // Erreurs de validation
                error_log("Erreurs de validation: " . print_r(Validator::getErrors(), true));
                Session::set('errors', Validator::getErrors());
            }
        }

        // Affichage du formulaire
        $this->render('login/inscription.html.php', [
            'old' => $_POST ?? [],
            'errors' => Session::isset('errors') ? $_SESSION['errors'] : [],
        ]);
        
        // Nettoyer les erreurs après affichage
        Session::unset('errors');
    }

    /**
     * Upload simple d'une photo
     */
    private function uploadSimple($file, $prefix = 'photo'): string
    {
        try {
            // Créer le dossier uploads s'il n'existe pas
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Vérifier l'extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                error_log("Extension non autorisée: " . $fileExtension);
                return '';
            }

            // Générer un nom unique
            $fileName = $prefix . '_' . uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                error_log("Photo uploadée avec succès: " . $fileName);
                return '/uploads/' . $fileName;
            } else {
                error_log("Erreur lors du déplacement du fichier");
                return '';
            }

        } catch (\Exception $e) {
            error_log("Erreur upload: " . $e->getMessage());
            return '';
        }
    }

   

    public function edit()
    {
        $this->redirect('/');
    }

    public function show()
    {
        $this->redirect('/');
    }
     public function store()
    {
    }

    public function logout()
    {
        Session::destroy();
        $this->redirect('/');
    }

    private function redirect(string $url)
    {
        error_log("Redirection vers: " . $url);
        header("Location: $url");
        exit;
    }
}