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
        
        <!-- Formulaire simplifié -->
        <form method="POST" action="<?= APP_URL ?>/register" enctype="multipart/form-data">
            <div class="max-w-md mx-auto space-y-6">
                
                <!-- Numéro CNI -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">Numéro CNI (13 chiffres)</label>
                    <input type="text" 
                           id="num_carte_identite"
                           name="num_carte_identite"
                           value="<?= htmlspecialchars($old['num_carte_identite'] ?? '') ?>"
                           placeholder="Entrez votre numéro CNI" 
                           maxlength="13"
                           pattern="[0-9]{13}"
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['num_carte_identite']) ? 'border-2 border-red-500' : '' ?>">
                    
                    <div id="cni-loader" class="hidden mt-2">
                        <div class="flex items-center text-blue-500 text-sm">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Vérification en cours...
                        </div>
                    </div>
                    
                    <div id="cni-success" class="hidden mt-2">
                        <div class="flex items-center text-green-500 text-sm">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span id="cni-info"></span>
                        </div>
                    </div>
                    
                    <div id="cni-error" class="hidden mt-2">
                        <div class="flex items-center text-red-500 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span id="cni-error-msg"></span>
                        </div>
                    </div>
                    
                    <?php if (isset($errors['num_carte_identite'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['num_carte_identite']) ?>
                        </p>
                    <?php endif; ?>
                    <div class="text-gray-400 text-sm mt-1">Les informations personnelles seront récupérées automatiquement</div>
                </div>
                
                <!-- Téléphone -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">Numéro de téléphone</label>
                    <input type="tel" 
                           name="telephone"
                           value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                           placeholder="Entrez votre numéro de téléphone" 
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['telephone']) ? 'border-2 border-red-500' : '' ?>">
                    <?php if (isset($errors['telephone'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['telephone']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Password -->
                <div>
                    <label class="text-gray-700 text-sm block mb-2">Mot de passe</label>
                    <input type="password" 
                           name="password"
                           placeholder="Entrez votre mot de passe" 
                           class="w-full bg-gray-100 text-gray-900 placeholder-gray-400 border border-gray-300 outline-none focus:outline-none focus:text-gray-900 px-4 py-3 rounded-lg text-sm <?= isset($errors['password']) ? 'border-2 border-red-500' : '' ?>">
                    <?php if (isset($errors['password'])): ?>
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <?= htmlspecialchars($errors['password']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
            </div>
            
            <!-- Boutons -->
            <div class="flex justify-center space-x-4 mt-8">
                
                <!-- Bouton Créer le compte -->
                <button type="submit" class="bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-200 px-8 py-3 rounded-full text-sm font-medium">
                    Créer le compte
                </button>
                
                <!-- Bouton retour à la page de connexion -->
                <a href="<?= APP_URL ?>/" class="bg-red-500 text-white hover:bg-red-600 transition-colors duration-200 px-6 py-3 rounded-full text-sm font-medium inline-block">
                    retour à la page de connexion
                </a>
                
            </div>
            
        </form>
        
    </div>
    
    <!-- Inclure Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        let timeoutId;
        
        document.getElementById('num_carte_identite').addEventListener('input', function(e) {
            const cni = e.target.value.trim();
            
            // Reset états
            hideAllMessages();
            
            // Vérifier si on a 13 chiffres
            if (cni.length === 13 && /^[0-9]{13}$/.test(cni)) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    verifierCNI(cni);
                }, 800); // Attendre 800ms après la saisie
            }
        });
        
        function hideAllMessages() {
            document.getElementById('cni-loader').classList.add('hidden');
            document.getElementById('cni-success').classList.add('hidden');
            document.getElementById('cni-error').classList.add('hidden');
        }
        
        async function verifierCNI(cni) {
            // Afficher le loader
            document.getElementById('cni-loader').classList.remove('hidden');
            
            try {
                const response = await fetch('/api/verifier-cni', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ nci: cni })
                });
                
                const data = await response.json();
                
                hideAllMessages();
                
                if (data.statut === 'success' && data.data) {
                    // CNI trouvé
                    document.getElementById('cni-info').textContent = 
                        `${data.data.prenom} ${data.data.nom}`;
                    document.getElementById('cni-success').classList.remove('hidden');
                } else {
                    // CNI non trouvé
                    document.getElementById('cni-error-msg').textContent = 
                        'Numéro CNI non trouvé dans la base nationale';
                    document.getElementById('cni-error').classList.remove('hidden');
                }
            } catch (error) {
                hideAllMessages();
                document.getElementById('cni-error-msg').textContent = 
                    'Erreur de vérification. Veuillez réessayer.';
                document.getElementById('cni-error').classList.remove('hidden');
            }
        }
    </script>
    
</body>
</html>
