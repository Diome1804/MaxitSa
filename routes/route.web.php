<?php

use App\Core\Router;
use Src\Controller\SecurityController;
use Src\Controller\InscriptionController;
use Src\Controller\CompteController;


// Routes d'authentification
Router::get('/login', SecurityController::class, 'index');        
Router::post('/login', SecurityController::class, 'store');      
Router::get('/logout', SecurityController::class, 'destroy');     

// Routes d'inscription
Router::get('/register', SecurityController::class, 'show');      
Router::post('/register', CompteController::class, 'store');     

// Routes du dashboard
Router::get('/dashboard', CompteController::class, 'index');      

// Route racine
Router::get('/', SecurityController::class, 'index');























// // Routes d'authentification avec SecurityController
// Router::get('/login', SecurityController::class, 'index');
// Router::post('/login', SecurityController::class, 'store');  // Pour traiter la connexion
// Router::get('/logout', SecurityController::class, 'destroy');

// // Routes d'inscription
// Router::get('/register', SecurityController::class, 'show');  // Afficher le formulaire
// Router::post('/register', CompteController::class, 'store');  // Traiter l'inscription

// // Routes du dashboard
// Router::get('/dashboard', CompteController::class, 'index');

// // Route racine
// Router::get('/', SecurityController::class, 'index');





















// Router::get('/logout', SecurityController::class, 'logout');

// // Routes des commandes (protégées)
// Router::get('/list', CommandeController::class, 'index', ['auth']);
// //Router::get('/facture', CommandeController::class, 'show', ['auth']);
// Router::get('/facture', FactureController::class, 'show', ['auth']);
// Router::get('/form', CommandeController::class, 'create', ['auth', 'isVendeur']);
// Router::get('/commande', CommandeController::class, 'create');

// Résoudre la route
Router::resolve();

