<?php

namespace App\Config\Core;
use App\core\Session;

abstract class AbstractController
{

 


    abstract public function index();

    abstract public function store();

    abstract public function create();


    abstract public function destroy();

    abstract public function show();

    abstract public function edit();

    protected function renderHtml(String $view, array $params = [])
    {
        extract($params); // rend $commandes disponible dans la vue

        ob_start();
        require_once '../template/' . $view;
        $contentForLayout = ob_get_clean();

        require_once '../template/layout/base.layout.php';
    }

    
    protected function renderHtmlLogin(String $view, array $params = [])
    {
        extract($params);
        require_once '../template/' . $view;
    }
}
