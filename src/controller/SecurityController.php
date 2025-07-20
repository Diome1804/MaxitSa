<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\SecurityService;
use Src\Repository\CompteRepository;
use App\Core\FileUpload;
use App\Core\Validator;
use App\Core\Session;
use App\Core\App;
use App\Core\MiddlewareLoader;
use App\Core\Lang;

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

        // Détecter et configurer la langue
        Lang::detectLang();
    }
    public function index()
    {
        if (Session::isset('user')) {
            $this->redirect(APP_URL . '/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['numero']) || empty($_POST['password'])) {
                Session::set('errors', ['login' => 'Numéro et mot de passe requis']);
            } else {
                $user = $this->securityService->authenticate($_POST);

                if ($user) {
                    Session::set('user', $user->toArray());
                    // Rediriger vers la page de tableau de bord
                    header("Location: /dashboard");
                    exit();
                } else {
                    Session::set('errors', ['login' => 'Numéro ou mot de passe incorrect']);
                }
            }
        }
        $this->render("login/login.html.php", [
            'success' => Session::get('success'),
            'old' => $_POST ?? [],
            'errors' => Session::get('errors'),
        ]);
        Session::unset('success');
        Session::unset('errors');
    }


    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $rules = [
                'nom' => ['required', ['minLength', 2, 'Le nom doit contenir au moins 2 caractères']],
                'prenom' => ['required', ['minLength', 2, 'Le prénom doit contenir au moins 2 caractères']],
                'telephone' => ['required', 'isSenegalPhone'],
                'password' => ['required', 'isPassword'],
                'adresse' => ['required', ['minLength', 5, 'L\'adresse doit contenir au moins 5 caractères']],
                'num_carte_identite' => ['required', 'isCNI']
            ];

            if ($this->validator->validate($_POST, $rules)) {
                try {

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

                    $userId = $this->securityService->creerClientAvecComptePrincipal($userData, 0.0);

                    if ($userId !== false) {
                        Session::set('success', 'Inscription réussie ! Votre compte principal a été créé.');
                        $this->redirect(APP_URL . '/');
                        exit;
                    } else {
                        Session::set('errors', ['general' => 'Erreur lors de l\'inscription. Veuillez réessayer.']);
                    }

                } catch (\Exception $e) {
                    Session::set('errors', ['general' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
                }
            } else {
                Session::set('errors', Validator::getErrors());
            }
        }
        $this->render('login/inscription.html.php', [
            'old' => $_POST ?? [],
            'errors' => Session::get('errors'),
        ]);
        Session::unset('errors');
    }

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
        //$this->redirect('/');
    }

    public function show()
    {
        //$this->redirect('/');
    }
    public function store()
    {
    }

    // public function logout()
    // {
    //     Session::destroy();
    //     $this->redirect('/');
    // }
    public function logout()
    {
        Session::destroy();
        $this->redirect(APP_URL . '/');
    }

    private function redirect(string $url)
    {
        error_log("Redirection vers: " . $url);
        header("Location: $url");
        exit;
    }
}