<?php

use App\Config\Core\Router;
use App\Controller\CommandeController;
use App\Controller\SecurityController;
use App\Controller\FactureController;

// Routes de sécurité
Router::get('/login', SecurityController::class, 'index');
Router::post('/login', SecurityController::class, 'login');


Router::get('/logout', SecurityController::class, 'logout');

// Routes des commandes (protégées)
Router::get('/list', CommandeController::class, 'index', ['auth']);
//Router::get('/facture', CommandeController::class, 'show', ['auth']);
Router::get('/facture', FactureController::class, 'show', ['auth']);
Router::get('/form', CommandeController::class, 'create', ['auth', 'isVendeur']);
Router::get('/commande', CommandeController::class, 'create');

// Résoudre la route
Router::resolve();

