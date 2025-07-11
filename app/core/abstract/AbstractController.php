<?php

namespace App\Core\Abstract;
use App\core\Session;

abstract class AbstractController
{

 


    abstract public function index();

    abstract public function store();

    abstract public function create(); 


    abstract public function destroy();

    abstract public function show();

    abstract public function edit();


    // protected function renderHtml(String $view, array $params = [])
    // {
    //     extract($params); // rend $commandes disponible dans la vue

    //     ob_start();
    //     require_once '../templates/' . $view;
    //     $contentForLayout = ob_get_clean();

    //     require_once '../templates/layout/base.layout.php';
    // }

    
    protected function renderHtmlLogin(String $view, array $params = [])
    {
        extract($params);
        //require_once '../../templates/dashboard/dashboard.html.php';
        require_once '../templates/' . $view;
    }


     protected function render(string $view, array $data = []): void
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Chemin vers le template
        $viewPath = '../templates/' . $view;
        // var_dump($viewPath);
        // die();
        // Si le chemin ne contient pas d'extension, ajouter .php
        if (!pathinfo($viewPath, PATHINFO_EXTENSION)) {
            $viewPath .= '.php';
        }
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new \Exception("Vue non trouvée : " . $viewPath);
        }
    }



    // public function render(string $view, array $data = []): void
    
    // {
    // extract($data);
    // require_once '../templates/' . $view;
    // }

    
}
