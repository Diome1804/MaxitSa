<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXITSA - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            transition: all 0.3s ease;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        }
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        }
        .input-group:focus-within {
            transform: scale(1.02);
        }
        .btn-login {
            transition: all 0.3s ease;
            letter-spacing: 1px;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
        .btn-login:active {
            transform: translateY(1px);
        }
    </style>
</head>
<body>
    <div class="w-full max-w-md mx-4">
        <div class="login-card bg-white rounded-2xl overflow-hidden">
            <!-- En-tête avec MacBook Pro mention -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-4 px-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold">MAXITSA</h1>
                    <div class="text-xs bg-blue-800 px-2 py-1 rounded-full">
                        <i class="fas fa-laptop mr-1"></i> MacBook Pro 14"
                    </div>
                </div>
            </div>
            
            <!-- Contenu principal -->
            <div class="py-8 px-8">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Connectez-vous</h2>
                    <p class="text-gray-500">Accédez à votre espace personnel</p>
                </div>
                
                <form>
                    <!-- Champ Numéro -->
                    <div class="mb-6 input-group">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="numero">
                            Numéro
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <i class="fas fa-user"></i>
                            </span>
                            <input 
                                id="numero" 
                                type="text" 
                                class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" 
                                placeholder="Entrez votre numéro"
                                required>
                        </div>
                    </div>
                    
                    <!-- Champ Password -->
                    <div class="mb-8 input-group">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="password">
                            Mot de passe
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input 
                                id="password" 
                                type="password" 
                                class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" 
                                placeholder="Entrez votre mot de passe"
                                required>
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="text-right mt-2">
                            <a href="#" class="text-sm text-blue-600 hover:underline">Mot de passe oublié ?</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Se Connecter -->
                    <div class="mb-6">
                        <button type="submit" class="btn-login w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg shadow-md">
                            Se Connecter
                        </button>
                    </div>
                    
                    <!-- Lien Pas encore de compte -->
                    <div class="text-center">
                        <p class="text-gray-600">
                            Pas encore de compte ? 
                            <a href="#" class="text-blue-600 font-medium hover:underline">Créer un compte</a>
                        </p>
                    </div>
                </form>
            </div>
            
            <!-- Pied de page -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-center">
                <div class="flex justify-center space-x-6 mb-3">
                    <a href="#" class="text-gray-500 hover:text-blue-600">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-400">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-red-600">
                        <i class="fab fa-google"></i>
                    </a>
                </div>
                <p class="text-xs text-gray-500">© 2025 MAXITSA. Tous droits réservés.</p>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour basculer la visibilité du mot de passe
        document.querySelector('.fa-eye').parentElement.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
        
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.login-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>