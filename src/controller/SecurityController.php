<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\SecurityService;
use Src\Controller\CompteController;
use App\Core\Session;

class SecurityController extends AbstractController
{
    private SecurityService $securityService;
    private Session $session;

    public function __construct(){
        
        //$this->securityService = SecurityService::getInstance();
        $this->session = Session::getInstance();
    }

    public function index()
    {   
         //$this->render('login', $data);
        $this->renderHtmlLogin('login/login.html.php');
    }

    public function show() {
        $this->renderHtmlLogin('login/inscription.html.php');
    }

    public function store() {
        // TODO: Logique de connexion
        // Récupérer les données POST
        // Valider les credentials
        // Si OK : rediriger vers dashboard
        // Si KO : afficher erreur
    }

    public function create() {}

    public function destroy() {
        // ✅ Maintenant $this->session est initialisée
        $this->session->destroy();
        
        // Redirection vers login après déconnexion
        header('Location: /login');
        exit;
    }

    public function edit() {}
}
