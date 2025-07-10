<?php

namespace Src\Controller;

use Src\Service\UserService;
use Src\Service\CompteService;
use App\Core\Abstract\AbstractController;

class UserController extends AbstractController
{
    private UserService $userService;
    private CompteService $compteService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->compteService = new CompteService();
    }


    
    public function index(){}
    public function show(){}
    public function store() {}
    public function create() {}
    public function destroy() {}
    public function edit() {}
}
