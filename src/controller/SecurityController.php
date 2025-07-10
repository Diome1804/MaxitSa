<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\SecurityService;
use Src\Repository\UserRepository;

class SecurityController extends AbstractController
{
    private SecurityService $securityService;
    private UserRepository $userRepository;

    public function __construct(){
        $this->userRepository = UserRepository::getInstance();
        //$this->securityService = SecurityService::getInstance();
    }

    public function index()
    {   
        // Afficher le formulaire de connexion
        $this->renderHtmlLogin('login/login.html.php');
    }

    public function show() {
        // Afficher le formulaire d'inscription
        $this->renderHtmlLogin('login/inscription.html.php');
    }

    /**
     * ✅ GESTION DE LA CONNEXION
     */
    public function store() 
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        // Récupérer les données du formulaire
        $numero = trim($_POST['numero'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation basique
        $errors = [];
        
        if (empty($numero)) {
            $errors[] = "Le numéro de téléphone est requis";
        }
        
        if (empty($password)) {
            $errors[] = "Le mot de passe est requis";
        }

        // Si erreurs de validation
        if (!empty($errors)) {
            $this->renderHtmlLogin('login/login.html.php', ['errors' => $errors]);
            return;
        }

        try {
            // Chercher l'utilisateur par téléphone
            $user = $this->userRepository->findByTelephone($numero);
            
            if (!$user) {
                $errors[] = "Numéro ou mot de passe incorrect";
                $this->renderHtmlLogin('login/login.html.php', ['errors' => $errors]);
                return;
            }

            // Vérifier le mot de passe
            if (!password_verify($password, $user['password'])) {
                $errors[] = "Numéro ou mot de passe incorrect";
                $this->renderHtmlLogin('login/login.html.php', ['errors' => $errors]);
                return;
            }

            // ✅ CONNEXION RÉUSSIE - Créer la session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_telephone'] = $user['telephone'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];

            // Rediriger vers le dashboard
            //$this->redirect('/dashboard');
            header('Location: /dashboard');
            exit();

        } catch (\Exception $e) {
            error_log("Erreur connexion: " . $e->getMessage());
            $errors[] = "Erreur lors de la connexion. Veuillez réessayer.";
            $this->renderHtmlLogin('login/login.html.php', ['errors' => $errors]);
        }
    }

    public function create() {}

    public function destroy() {
        session_start();
        
        // Détruire toutes les variables de session
        $_SESSION = array();
        
        // Détruire la session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Rediriger vers la page de connexion
        //$this->redirect('/login');
        header('Location: /login');
    }

    public function edit() {}
}
