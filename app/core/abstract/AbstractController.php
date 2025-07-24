<?php

namespace App\Core\Abstract;

use App\Core\Session;
use App\Core\Interfaces\ControllerInterface;

abstract class AbstractController implements ControllerInterface
{
    protected Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    abstract public function index();
    abstract public function create();
    abstract public function store();
    abstract public function edit();
    abstract public function show();

    
                public function render(string $view, array $data = []): void
            {
                extract($data);
                require_once '../templates/' . $view;
            }

             


    // public function render(string $views, array $data = []){
    //     extract($data);
    //     ob_start();
    //     require_once '../templates/'.$views;
    //     $contentForLayout = ob_get_clean();
    //     require_once '../templates/layout/'. $this->layout . '.layout.php';
    // }

}