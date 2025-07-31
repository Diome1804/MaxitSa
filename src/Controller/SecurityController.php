<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\SecurityService;
use Src\Service\AppDAFService;
use Src\Repository\CompteRepository;
use App\Core\FileUpload;
use App\Core\Validator;
use App\Core\Session;
use App\Core\MiddlewareLoader;
use App\Core\Lang;

class SecurityController extends AbstractController
{
    private SecurityService $securityService;
    private AppDAFService $appDAFService;
    private CompteRepository $compteRepository;
    private Validator $validator;
    private FileUpload $fileUpload;

    public function __construct(Session $session, SecurityService $securityService, AppDAFService $appDAFService, CompteRepository $compteRepository, Validator $validator, FileUpload $fileUpload)
    {
        parent::__construct($session);
        $this->securityService = $securityService;
        $this->appDAFService = $appDAFService;
        $this->compteRepository = $compteRepository;
        $this->validator = $validator;
        $this->fileUpload = $fileUpload;
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
                'telephone' => ['required', 'isSenegalPhone'],
                'password' => ['required'],
                'num_carte_identite' => ['required']
            ];

            if ($this->validator->validate($_POST, $rules)) {
                try {
                    // Vérifier la CNI dans AppDAF de façon obligatoire
                    $citoyenData = null;
                    $apiDisponible = true;
                    
                    try {
                        $citoyenData = $this->appDAFService->rechercherCitoyenParCNI(trim($_POST['num_carte_identite']));
                    } catch (\Exception $e) {
                        // L'API AppDAF est indisponible
                        $apiDisponible = false;
                        error_log("AppDAF indisponible lors de l'inscription: " . $e->getMessage());
                    }

                    // Rejeter l'inscription si l'API est disponible mais la CNI n'est pas trouvée
                    if ($apiDisponible && !$citoyenData) {
                        Session::set('errors', ['num_carte_identite' => 'Ce numéro CNI n\'est pas trouvé dans la base de données nationale. Veuillez vérifier votre numéro.']);
                        $this->render('login/inscription.html.php', [
                            'old' => $_POST ?? [],
                            'errors' => Session::get('errors'),
                        ]);
                        Session::unset('errors');
                        return;
                    }
                    
                    // Si l'API est indisponible, rejeter complètement l'inscription
                    if (!$apiDisponible) {
                        Session::set('errors', ['general' => 'Le service de vérification des CNI est temporairement indisponible. Veuillez réessayer plus tard.']);
                        $this->render('login/inscription.html.php', [
                            'old' => $_POST ?? [],
                            'errors' => Session::get('errors'),
                        ]);
                        Session::unset('errors');
                        return;
                    }

                    // Construire les données utilisateur avec les informations de AppDAF
                    $userData = [
                        'nom' => $citoyenData['nom'],
                        'prenom' => $citoyenData['prenom'],
                        'adresse' => $citoyenData['lieuNaissance'] ?? 'Adresse non renseignée',
                        'num_carte_identite' => trim($_POST['num_carte_identite']),
                        'telephone' => trim($_POST['telephone']),
                        'password' => $_POST['password'],
                        'photorecto' => $citoyenData['photoIdentiteUrl'] ?? '',
                        'photoverso' => '',
                    ];
                    
                    // Créer le compte avec un solde initial de 1000 FCFA
                    $userId = $this->securityService->creerClientAvecComptePrincipal($userData, 1000.0);

                    if ($userId !== false) {
                        Session::set('success', 'Inscription réussie ! Votre compte principal a été créé.');
                        $this->redirect(APP_URL . '/');
                        exit;
                    } else {
                        Session::set('errors', ['general' => 'Erreur lors de l\'inscription. Veuillez réessayer.']);
                    }

                } catch (\Exception $e) {
                    error_log("Erreur d'inscription complète: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
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
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                error_log("Extension non autorisée: " . $fileExtension);
                return '';
            }
            $fileName = $prefix . '_' . uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
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


    public function logout()
    {
        Session::destroy();
        $this->redirect(APP_URL . '/');
    }

    public function verifierCNI()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['statut' => 'error', 'message' => 'Méthode non autorisée']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['nci']) || empty($input['nci'])) {
            http_response_code(400);
            echo json_encode(['statut' => 'error', 'message' => 'CNI requis']);
            return;
        }

        try {
            $citoyenData = $this->appDAFService->rechercherCitoyenParCNI(trim($input['nci']));
            
            if ($citoyenData !== null) {
                echo json_encode([
                    'statut' => 'success',
                    'data' => $citoyenData
                ]);
            } else {
                echo json_encode([
                    'statut' => 'error',
                    'message' => 'CNI non trouvé'
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'statut' => 'error',
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    private function redirect(string $url)
    {
        error_log("Redirection vers: " . $url);
        header("Location: $url");
        exit;
    }
}