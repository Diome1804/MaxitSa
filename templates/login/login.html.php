<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXITSA - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">
    
    <!-- Zone principale blanche -->
    <div class="bg-white w-full max-w-md mx-auto p-8 rounded-lg shadow">
        
        <!-- Titre Connexion -->
        <div class="text-center mb-8">
            <h1 class="text-gray-900 text-2xl font-bold mb-2">
                Connexion
            </h1>
        </div>
        
        <?php if (isset($success) && $success): ?>
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6 text-center">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($errors['login'])): ?>
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6 text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= htmlspecialchars($errors['login']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($errors['telephone'])): ?>
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6 text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= htmlspecialchars($errors['telephone']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire de connexion -->
        <form method="POST" action="/login">
            <div class="space-y-4 w-80">
                <!-- Champ numéro -->
                <div>
                    <input type="text" 
                           name="numero"
                           placeholder="Entrez votre numéro"
                           value="<?= htmlspecialchars($old['numero'] ?? '') ?>"
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-5 py-4 rounded-lg text-sm">
                </div>
                
                <!-- Champ mot de passe -->
                <div>
                    <input type="password" 
                           name="password"
                           placeholder="Entrez votre mot de passe"
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-5 py-4 rounded-lg text-sm">
                </div>
                
                <!-- Bouton de connexion -->
                <div class="pt-4">
                    <button type="submit" class="bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-200 px-6 py-3 rounded-full text-sm font-medium">
                        Se Connecter
                    </button>
                </div>
                
                <div class="text-gray-700 text-sm mt-2 text-center">
                    <a href="/register" class="text-blue-500 underline hover:text-blue-700 transition-colors duration-200">
                        Pas encore de compte ?
                    </a>
                </div>
            </div>
        </form>
    </div>
    <!-- Inclure Font Awesome pour les icônes -->
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>