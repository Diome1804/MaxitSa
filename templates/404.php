<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Introuvable - MAXITSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .animate-pulse-slow {
            animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.03); }
        }
        .bounce {
            animation: bounce 3s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-20px);}
            60% {transform: translateY(-10px);}
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-lg w-full mx-4">
        <div class="text-center animate-pulse-slow">
            <!-- Illustration 404 -->
            <div class="relative mb-8">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-[12rem] font-bold text-gray-200">404</div>
                </div>
                <div class="relative">
                    <div class="bounce inline-block">
                        <svg class="w-64 h-64 mx-auto" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="95" stroke="#93c5fd" stroke-width="10" stroke-dasharray="10 5"/>
                            <circle cx="100" cy="100" r="80" stroke="#3b82f6" stroke-width="8"/>
                            <path d="M140 60L60 140" stroke="#ef4444" stroke-width="8" stroke-linecap="round"/>
                            <path d="M60 60L140 140" stroke="#ef4444" stroke-width="8" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Message -->
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Page Introuvable</h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Oups ! Il semble que la page que vous cherchez ait été déplacée, supprimée ou n'existe pas.
            </p>
            
            <!-- Bouton d'action -->
            <div class="mb-10">
                <a href="<?= APP_URL ?>/" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                    <i class="fas fa-home mr-2"></i>Retour à l'accueil
                </a>
            </div>
            
            <!-- Recherche -->
            <div class="max-w-md mx-auto mb-8">
                <p class="text-gray-500 mb-3">Ou essayez une recherche :</p>
                <div class="flex">
                    <input type="text" placeholder="Rechercher sur le site..." class="flex-grow px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-r-lg">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <!-- Navigation rapide -->
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#" class="text-blue-600 hover:underline"><i class="fas fa-user mr-1"></i> Compte</a>
                <a href="#" class="text-blue-600 hover:underline"><i class="fas fa-credit-card mr-1"></i> Services</a>
                <a href="#" class="text-blue-600 hover:underline"><i class="fas fa-question-circle mr-1"></i> Support</a>
                <a href="#" class="text-blue-600 hover:underline"><i class="fas fa-envelope mr-1"></i> Contact</a>
            </div>
        </div>
    </div>
    
    <script>
        // Animation supplémentaire au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('h1, p, div');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                setTimeout(() => {
                    el.style.transition = `opacity 0.5s ease ${index * 0.1}s`;
                    el.style.opacity = '1';
                }, 100);
            });
        });
    </script>
</body>
</html>