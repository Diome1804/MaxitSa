<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXITSA - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex flex-col font-sans">
    <!-- Header avec MacBook Pro -->
    <!-- <div class="text-gray-500 text-sm p-4">
        MacBook Pro 14" - 1
    </div> -->
    
    <!-- Zone principale orange -->
    <div class="flex-1 w-100 bg-orange-500 flex items-center justify-center">
        <div class="text-center">
            <!-- Titre MAXITSA -->
            <h1 class="text-white text-3xl font-bold mb-12 tracking-wide">
                MAXITSA
            </h1>
            
            <!-- Formulaire de connexion -->
            <form method="POST" action="/login">
                <div class="space-y-4 w-80">
                    <!-- Champ numéro -->
                    <div>
                        <input type="text" 
                               name="numero"
                               placeholder="entrez numero"
                               required
                               class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-5 py-4 rounded-lg text-sm">
                    </div>
                    
                    <!-- Champ mot de passe -->
                    <div>
                        <input type="password" 
                               name="password"
                               placeholder="entrez password"
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
                        <a href="/register" class="text-black underline hover:text-gray-200 transition-colors duration-200"> Pas encore de compte </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>