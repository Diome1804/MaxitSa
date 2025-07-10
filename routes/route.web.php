<?php

use App\Core\Router;
use Src\Controller\SecurityController;





// Routes de sécurité
Router::get('/login', SecurityController::class, 'index');
Router::get('/register', SecurityController::class, 'show');
Router::post('/register', SecurityController::class, 'createCompte');



// Router::get('/logout', SecurityController::class, 'logout');

// // Routes des commandes (protégées)
// Router::get('/list', CommandeController::class, 'index', ['auth']);
// //Router::get('/facture', CommandeController::class, 'show', ['auth']);
// Router::get('/facture', FactureController::class, 'show', ['auth']);
// Router::get('/form', CommandeController::class, 'create', ['auth', 'isVendeur']);
// Router::get('/commande', CommandeController::class, 'create');

// Résoudre la route
Router::resolve();

