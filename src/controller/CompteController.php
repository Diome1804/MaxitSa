<?php

namespace Src\Controller;

use App\Core\Abstract\AbstractController;

class CompteController extends AbstractController {

    public function create() {}
    public function store() {}
    public function show() {}
    public function edit() {}
    public function delete() {}

    public function index() 
    {
        $this->render('dashboard/dashboard.html.php');
    }

    // public function createComptePrincipal() 
    // {
    //     // Logique pour créer un compte principal
    //     $this->render('compte/create.html.php');
    // }
}