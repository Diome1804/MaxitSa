<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;
use Src\Service\SecurityService;

class SecurityController extends AbstractController
{
    private SecurityService $securityService;
    private Session $session;

    public function __construct(){
        //$this->securityService = SecurityService::getInstance();
        //$this->session = Session::getInstance();
    }

    public function index()
    {
        $this->renderHtmlLogin('login/login.html.php');
    }

    public function show() {
        $this->renderHtmlLogin('login/inscription.html.php');
    }



    public function store() {}
    public function create() {}
    public function destroy() {}
    
    public function edit() {}
}
