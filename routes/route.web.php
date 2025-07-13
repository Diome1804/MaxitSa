<?php 

use Src\Controller\CompteController;
use Src\Controller\SecurityController;


$routes = [
    '/' => [
        'controller' => SecurityController::class, 
        'method' => 'index'
    ],
     '/register' => [
         'controller' => SecurityController::class, 
         'method' => 'create',
     ],


     '/dashboard' => [
         'controller' => CompteController::class,
         'method' => 'index',
         'middlewares' => ['auth']

     ],
     "/login" => [
         'controller' => SecurityController::class, 
         'method' => 'index' 
     ],
     "/principalCreated" => [
         'controller' => CompteController::class,
         'method' => 'createComptePrincipal'
     ],
    "/logout" => [
         'controller' => SecurityController::class, 
         'method' => 'logout'
    ],
];