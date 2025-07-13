<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXITSA - Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">
    
    <!-- Zone principale blanche -->
    <div class="bg-white w-full max-w-4xl mx-auto p-8 rounded-lg shadow">
        
        <!-- Titre Inscription -->
        <h1 class="text-gray-900 text-2xl font-bold text-center mb-8">Inscription</h1>
        
        <!-- Message d'erreur général -->
        <?php if (isset($errors['general'])): ?>
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6 text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Message de succès -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6 text-center">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <!-- Formulaire en deux colonnes -->
        <form method="POST" action="/register" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Colonne gauche -->
            <div class="space-y-6">
                
                <!-- Nom -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">Nom</label>
                    <input type="text" 
                           name="nom"
                           value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                           placeholder="entrer votre nom" 
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['nom']) ? 'border-2 border-red-500' : '' ?>">
                    <?php if (isset($errors['nom'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['nom']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Prénom -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">Prenom</label>
                    <input type="text" 
                           name="prenom"
                           value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                           placeholder="entrer votre prenom" 
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['prenom']) ? 'border-2 border-red-500' : '' ?>">
                    <?php if (isset($errors['prenom'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['prenom']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Téléphone -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">Telephone</label>
                    <input type="tel" 
                           name="telephone"
                           value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                           placeholder="Entrer votre numero" 
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['telephone']) ? 'border-2 border-red-500' : '' ?>">
                    <?php if (isset($errors['telephone'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['telephone']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Adresse -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">Adresse</label>
                    <input type="text" 
                           name="adresse"
                           value="<?= htmlspecialchars($old['adresse'] ?? '') ?>"
                           placeholder="adresse" 
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['adresse']) ? 'border-2 border-red-500' : '' ?>">
                    <?php if (isset($errors['adresse'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['adresse']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Numéro CNI -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">numero CNI</label>
                    <input type="text" 
                           name="num_carte_identite"
                           value="<?= htmlspecialchars($old['num_carte_identite'] ?? '') ?>"
                           placeholder="votre CNI" 
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['num_carte_identite']) ? 'border-2 border-red-500' : '' ?>">
                    <?php if (isset($errors['num_carte_identite'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['num_carte_identite']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
            </div>
            
            <!-- Colonne droite -->
            <div class="space-y-6">
                
                <!-- Password -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">password</label>
                    <input type="password" 
                           name="password"
                           placeholder="password" 
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['password']) ? 'border-2 border-red-500' : '' ?>">
                    <?php if (isset($errors['password'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['password']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Photo carte d'identité recto -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">photo carte d'identité recto (optionnel)</label>
                    <input type="file"
                           name="photorecto"
                           accept="image/*"
                           class="w-full bg-gray-100 text-gray-900 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                    <div class="text-gray-400 text-sm mt-1">JPG, PNG, GIF acceptés</div>
                </div>
                
                <!-- Photo carte d'identité verso -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">photo carte d'identité verso (optionnel)</label>
                    <input type="file"
                           name="photoverso"
                           accept="image/*"
                           class="w-full bg-gray-100 text-gray-900 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                    <div class="text-gray-400 text-sm mt-1">JPG, PNG, GIF acceptés</div>
                </div>
                
            </div>
            
            <!-- Boutons -->
            <div class="flex justify-center space-x-4 mt-8 col-span-2">
                
                <!-- Bouton Créer le compte -->
                <button type="submit" class="bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-200 px-8 py-3 rounded-full text-sm font-medium">
                    Créer le compte
                </button>
                
                <!-- Bouton retour à la page de connexion -->
                <a href="/" class="bg-red-500 text-white hover:bg-red-600 transition-colors duration-200 px-6 py-3 rounded-full text-sm font-medium inline-block">
                    retour à la page de connexion
                </a>
                
            </div>
            
        </form>
        
    </div>
    
    <!-- Inclure Font Awesome pour les icônes -->
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
    
</body>
</html>
