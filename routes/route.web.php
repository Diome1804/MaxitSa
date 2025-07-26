<?php 

use Src\Controller\CompteController;
use Src\Controller\SecurityController;
use Src\Controller\WoyofalController;


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
     "/change-lang" => [
         'controller' => CompteController::class,
         'method' => 'changeLang'
     ],
    "/logout" => [
    'controller' => SecurityController::class, 
    'method' => 'logout'
    ],
    "/api/verifier-cni" => [
        'controller' => SecurityController::class,
        'method' => 'verifierCNI'
    ],
    
    // Routes Woyofal
    "/woyofal" => [
        'controller' => WoyofalController::class,
        'method' => 'index',
        'middlewares' => ['auth']
    ],
    "/woyofal/acheter" => [
        'controller' => WoyofalController::class,
        'method' => 'acheter',
        'middlewares' => ['auth']
    ],
    "/woyofal/recu" => [
        'controller' => WoyofalController::class,
        'method' => 'recu',
        'middlewares' => ['auth']
    ],
    "/woyofal/historique" => [
        'controller' => WoyofalController::class,
        'method' => 'historique',
        'middlewares' => ['auth']
    ],
];