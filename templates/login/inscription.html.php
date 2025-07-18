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
        
        <!-- Affichage des erreurs -->
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire en deux colonnes -->
        <form method="POST" action="/register" enctype="multipart/form-data">
            <div class="grid grid-cols-2 gap-8">
            
            <!-- Colonne gauche -->
            <div class="space-y-6">
                
                <!-- Nom -->
                <div>
                    <label class="text-white text-sm block mb-2">Nom</label>
                    <input type="text" 
                           name="nom"
                           placeholder="entrer votre nom" 
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                           required
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Prénom -->
                <div>
                    <label class="text-white text-sm block mb-2">Prenom</label>
                    <input type="text" 
                           name="prenom"
                           placeholder="entrer votre prenom" 
                           value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
                           required
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Téléphone -->
                <div>
                    <label class="text-white text-sm block mb-2">Telephone</label>
                    <input type="tel" 
                           name="telephone"
                           placeholder="Entrer votre numero" 
                           value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>"
                           required
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Adresse -->
                <div>
                    <label class="text-white text-sm block mb-2">Adresse</label>
                    <input type="text" 
                           name="adresse"
                           placeholder="adresse" 
                           value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>"
                           required
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Numéro CNI -->
                <div>
                    <label class="text-white text-sm block mb-2">numero CNI</label>
                    <input type="text" 
                           name="num_carte_identite"
                           placeholder="votre CNI" 
                           value="<?= htmlspecialchars($_POST['num_carte_identite'] ?? '') ?>"
                           required
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
            </div>
            
            <!-- Colonne droite -->
            <div class="space-y-6">
                
                <!-- Password -->
                <div>
                    <label class="text-white text-sm block mb-2">password</label>
                    <input type="password" 
                           name="password"
                           placeholder="password" 
                           required
                           class="w-full bg-gray-800 text-gray-400 placeholder-gray-500 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm">
                </div>
                
                <!-- Photo carte d'identité recto -->
                <div>
                    <label class="text-white text-sm block mb-2">photo carte d'identité recto</label>
                    <input type="file" 
                           name="photo_cni_recto"
                           accept="image/*"
                           required
                           class="w-full bg-gray-800 text-gray-400 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600">
                    <div class="text-gray-400 text-sm mt-1"></div>
                </div>
                
                <!-- Photo carte d'identité verso -->
                <div>
                    <label class="text-white text-sm block mb-2">photo carte d'identité verso</label>
                    <input type="file" 
                           name="photo_cni_verso"
                           accept="image/*"
                           required
                           class="w-full bg-gray-800 text-gray-400 border-none outline-none focus:outline-none focus:text-white px-4 py-3 rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600">
                    <div class="text-gray-400 text-sm mt-1"></div>
                </div>
                
            </div>
            
            <!-- Boutons -->
            <div class="flex justify-center space-x-4 mt-8">
                
                <!-- Bouton Créer le compte -->
                <button type="submit" class="bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors duration-200 px-8 py-3 rounded-full text-sm font-medium">
                    Créer le compte
                </button>
                
                <!-- Bouton retour à la page de connexion -->
                <a href="/login" class="bg-white text-gray-800 hover:bg-gray-100 transition-colors duration-200 px-6 py-3 rounded-full text-sm font-medium inline-block">
                    retour à la page de connexion
                </a>
                
            </div>
            
        </form>
        
    </div>
    
</body>
</html>