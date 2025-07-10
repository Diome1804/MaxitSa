<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXITSA - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center font-sans">
    
    <!-- Zone principale orange -->
    <div class="bg-orange-500 w-full max-w-md mx-auto p-8 rounded-lg">
        
        <!-- Titre Connexion -->
        <div class="text-center mb-8">
            <h1 class="text-white text-2xl font-bold mb-2">
                Connexion
            </h1>
            
            <!-- ✅ Affichage des erreurs -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-sm"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Formulaire de connexion -->
        <form method="POST" action="/login">
            <div class="space-y-4 w-80">
                <!-- Champ numéro -->
                <div>
                    <input type="text" 
                           name="numero"
                           placeholder="Entrez votre numéro"
                           value="<?= htmlspecialchars($_POST['numero'] ?? '') ?>"
                           required
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-5 py-4 rounded-lg text-sm">
                </div>
                
                <!-- Champ mot de passe -->
                <div>
                    <input type="password" 
                           name="password"
                           placeholder="Entrez votre mot de passe"
                           required
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-5 py-4 rounded-lg text-sm">
                </div>
                
                <!-- Bouton de connexion -->
                <div class="pt-4">
                    <button type="submit" class="bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors duration-200 px-6 py-3 rounded-full text-sm font-medium">
                        Se Connecter
                    </button>
                </div>
                
                <div class="text-black-400 text-sm mt-2">
                    <a href="/register" class="text-black underline hover:text-gray-200 transition-colors duration-200">
                        Pas encore de compte ?
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>