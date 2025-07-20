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
     "/create" => [
         'controller' => CompteController::class,
         'method' => 'createCompteSecondaire'
     ],
     "/changer-principal" => [
         'controller' => CompteController::class,
         'method' => 'changerPrincipal'
     ],
     "/transactions" => [
         'controller' => CompteController::class,
         'method' => 'transactions',
         'middlewares' => ['auth']
     ],
    "/logout" => [
         'controller' => SecurityController::class, 
         'method' => 'logout'
    ],
];