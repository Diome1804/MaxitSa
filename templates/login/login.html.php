<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXITSA - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-white min-h-screen flex items-center justify-center font-sans relative overflow-hidden">
    
    <!-- Éléments décoratifs en arrière-plan -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-100 opacity-20 rounded-full animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-100 opacity-15 rounded-full animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/4 w-20 h-20 bg-blue-200 opacity-30 rounded-full animate-float" style="animation-delay: -1s;"></div>
    </div>
    
    <!-- Zone principale -->
    <div class="bg-white w-full max-w-md mx-auto p-8 rounded-2xl shadow-2xl relative z-10 transform hover:scale-105 transition-transform duration-300 border border-gray-100">
        
        <!-- Logo/Icône -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-user-circle text-white text-3xl"></i>
            </div>
            <h1 class="text-gray-800 text-3xl font-bold mb-2 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                MAXITSA
            </h1>
            <p class="text-gray-600 text-sm">Connectez-vous à votre compte</p>
        </div>
        
        <?php if (isset($success) && $success): ?>
            <div class="bg-gradient-to-r from-green-400 to-green-600 text-white p-4 rounded-xl mb-6 text-center shadow-lg animate-pulse">
                <i class="fas fa-check-circle mr-2 text-lg"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($errors['login'])): ?>
            <div class="bg-gradient-to-r from-red-400 to-red-600 text-white p-4 rounded-xl mb-6 text-center shadow-lg">
                <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                <?= htmlspecialchars($errors['login']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($errors['telephone'])): ?>
            <div class="bg-gradient-to-r from-red-400 to-red-600 text-white p-4 rounded-xl mb-6 text-center shadow-lg">
                <i class="fas fa-exclamation-triangle mr-2 text-lg"></i>
                <?= htmlspecialchars($errors['telephone']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire de connexion -->
        <form method="POST" action="<?= APP_URL ?>/login" class="space-y-6">
            
            <!-- Champ numéro -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-phone text-gray-400"></i>
                </div>
                <input type="text" 
                       name="numero"
                       placeholder="Entrez votre numéro"
                       value="<?= htmlspecialchars($old['numero'] ?? '') ?>"
                       class="input-focus w-full bg-white border-2 border-gray-200 text-gray-900 placeholder-gray-400 pl-12 pr-4 py-4 rounded-xl text-sm transition-all duration-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 shadow-sm">
            </div>
            
            <!-- Champ mot de passe -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" 
                       name="password"
                       placeholder="Entrez votre mot de passe"
                       class="input-focus w-full bg-white border-2 border-gray-200 text-gray-900 placeholder-gray-400 pl-12 pr-4 py-4 rounded-xl text-sm transition-all duration-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 shadow-sm">
            </div>
            
            <!-- Bouton de connexion -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white hover:from-blue-600 hover:to-purple-700 transition-all duration-300 px-6 py-4 rounded-xl text-sm font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Se Connecter
                </button>
            </div>
            
            <!-- Lien d'inscription -->
            <div class="text-center pt-4">
                <p class="text-gray-600 text-sm mb-2">Pas encore de compte ?</p>
                <a href="<?= APP_URL ?>/register" class="inline-flex items-center text-blue-600 hover:text-purple-600 transition-colors duration-300 font-medium">
                    <i class="fas fa-user-plus mr-2"></i>
                    Créer un compte
                </a>
            </div>
        </form>
    </div>
    
    <!-- Particules flottantes pour effet moderne -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/4 right-1/3 w-2 h-2 bg-blue-400 opacity-60 rounded-full animate-ping"></div>
        <div class="absolute bottom-1/4 left-1/3 w-3 h-3 bg-purple-400 opacity-40 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-3/4 right-1/4 w-1 h-1 bg-blue-500 opacity-80 rounded-full animate-ping" style="animation-delay: 1s;"></div>
    </div>
</body>
</html>