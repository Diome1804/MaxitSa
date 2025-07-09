<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXITSA - Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center font-sans">
    
    <!-- Zone principale orange -->
    <div class="bg-orange-500 w-full max-w-4xl mx-auto p-8 rounded-lg">
        
        <!-- Titre Inscription -->
        <h1 class="text-white text-2xl font-bold text-center mb-8">
            Inscription
        </h1>
        
        <!-- Formulaire en deux colonnes -->
        <div class="grid grid-cols-2 gap-8">
            
            <!-- Colonne gauche -->
            <div class="space-y-6">
                
                <!-- Nom -->
                <div>
                    <label class="text-white text-sm block mb-2">Nom</label>
                    <input type="text" 
                           placeholder="entrer votre nom" 
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Prénom -->
                <div>
                    <label class="text-white text-sm block mb-2">Prenom</label>
                    <input type="text" 
                           placeholder="entrer votre prenom" 
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Téléphone -->
                <div>
                    <label class="text-white text-sm block mb-2">Telephone</label>
                    <input type="tel" 
                           placeholder="Entrer votre numero" 
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Adresse -->
                <div>
                    <label class="text-white text-sm block mb-2">Adresse</label>
                    <input type="text" 
                           placeholder="adresse" 
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Numéro CNI -->
                <div>
                    <label class="text-white text-sm block mb-2">numero CNI</label>
                    <input type="text" 
                           placeholder="votre CNI" 
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
            </div>
            
            <!-- Colonne droite -->
            <div class="space-y-6">
                
                <!-- Password -->
                <div>
                    <label class="text-white text-sm block mb-2">password</label>
                    <input type="password" 
                           placeholder="password" 
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Photo carte d'identité recto -->
                <div>
                    <label class="text-white text-sm block mb-2">photo carte d'identité recto</label>
                    <input type="file" 
                           accept="image/*"
                           class="w-full bg-gray-800 text-gray-400 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600">
                    <div class="text-gray-400 text-sm mt-1"></div>
                </div>
                
                <!-- Photo carte d'identité verso -->
                <div>
                    <label class="text-white text-sm block mb-2">photo carte d'identité verso</label>
                    <input type="file" 
                           accept="image/*"
                           class="w-full bg-gray-800 text-gray-400 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600">
                    <div class="text-gray-400 text-sm mt-1"></div>
                </div>
                
            </div>
            
        </div>
        
        <!-- Boutons -->
        <div class="flex justify-center space-x-4 mt-8">
            
            <!-- Bouton Créer le compte -->
            <button class="bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors duration-200 px-8 py-3 rounded-full text-sm font-medium">
                Créer le compte
            </button>
            
            <!-- Bouton retour à la page de connexion -->
            <button class="bg-white text-gray-800 hover:bg-gray-100 transition-colors duration-200 px-6 py-3 rounded-full text-sm font-medium">
                <a href="/login">retour à la page de connexion</a>
            </button>
            
        </div>
        
    </div>
    
</body>
</html>