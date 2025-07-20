<?php

return [
    // Messages d'erreur généraux
    'validation' => [
        'required' => 'Ce champ est obligatoire',
        'email_invalid' => 'Adresse email invalide',
        'password_invalid' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial',
        'phone_invalid' => 'Numéro de téléphone invalide',
        'cni_invalid' => 'Numéro de CNI invalide',
        'min_length' => 'Ce champ est trop court',
        'date_invalid' => 'Date invalide',
        'amount_invalid' => 'Montant invalide',
        'amount_positive' => 'Le montant doit être supérieur à 0',
    ],

    // Messages d'authentification
    'auth' => [
        'login_required' => 'Vous devez être connecté pour accéder à cette page',
        'login_success' => 'Connexion réussie',
        'login_failed' => 'Identifiants incorrects',
        'logout_success' => 'Déconnexion réussie',
        'access_denied' => 'Accès refusé',
    ],

    // Messages de compte
    'account' => [
        'create_success' => 'Compte secondaire créé avec succès',
        'create_failed' => 'Erreur lors de la création du compte',
        'insufficient_balance' => 'Solde insuffisant dans le compte principal',
        'update_failed' => 'Erreur lors de la mise à jour du compte principal',
        'not_found' => 'Compte non trouvé ou non autorisé',
        'already_main' => 'Ce compte est déjà le compte principal',
        'no_main_account' => 'Aucun compte principal trouvé',
        'change_main_success' => 'Compte principal changé avec succès',
        'change_main_failed' => 'Erreur lors du changement de compte principal',
        'id_required' => 'ID de compte manquant',
        'id_invalid' => 'ID de compte invalide',
        'phone_required' => 'Numéro de téléphone requis',
        'amount_required' => 'Montant requis',
        'balance_initial_positive' => 'Le solde initial doit être supérieur à 0',
    ],

    // Messages de transaction
    'transaction' => [
        'date_range_invalid' => 'La date de début doit être antérieure à la date de fin',
        'filter_applied' => 'Filtres appliqués',
        'no_transactions' => 'Aucune transaction trouvée',
        'error_loading' => 'Erreur lors du chargement des transactions',
    ],

    // Messages généraux
    'general' => [
        'form_error' => 'Veuillez corriger les erreurs du formulaire',
        'server_error' => 'Erreur interne du serveur',
        'success' => 'Opération réussie',
        'error' => 'Une erreur est survenue',
        'invalid_request' => 'Requête invalide',
        'access_forbidden' => 'Accès interdit',
    ],
];
