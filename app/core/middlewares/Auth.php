<?php

namespace App\Core\Middlewares;

use App\Core\Session;

class Auth
{
    public function __invoke()
    {
        // ✅ AJOUTEZ DES HEADERS ANTI-CACHE
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        if (!Session::isset('user')) {
            error_log("❌ Middleware Auth: Utilisateur non connecté");
            header('Location: /');  // ✅ Changé de /login à /
            exit();
        }
        
        error_log("✅ Middleware Auth: Utilisateur connecté");
    }
}
