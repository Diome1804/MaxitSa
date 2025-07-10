<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAXITSA - Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen font-sans">
    
    <!-- Header -->
    <div class="bg-orange-500 flex items-center justify-between px-6 py-3">
        
        <!-- Logo et navigation -->
        <div class="flex items-center space-x-6">
            <div class="text-white text-lg font-bold">MAXIT</div>
            <nav class="flex space-x-4">
                <a href="#" class="text-white text-sm hover:text-gray-200">test</a>
                <a href="#" class="text-white text-sm hover:text-gray-200">test</a>
                <a href="#" class="text-white text-sm hover:text-gray-200">Frame</a>
            </nav>
        </div>
        
        <!-- Partie droite avec recherche, profil et déconnexion -->
        <div class="flex items-center space-x-4">
            <!-- Icône de recherche -->
            <div class="text-white cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            <!-- Photo de profil -->
            <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            
            <!-- Bouton déconnexion -->
            <button class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">
                <a href="/logout">deconnexion</a>
                
            </button>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="bg-orange-500 min-h-screen p-6">
        
        <!-- Titre principal -->
        <h1 class="text-white text-2xl font-bold mb-8">Tableau de bord</h1>
        
        <!-- Section Solde du compte -->
        <div class="mb-8">
            <h2 class="text-white text-lg font-semibold mb-4">Solde du compte</h2>
            
            <!-- Dropdown solde -->
            <div class="bg-gray-800 rounded-lg p-4 w-80 flex items-center justify-between cursor-pointer">
                <div>
                    <div class="text-white font-semibold">10.000 FCFA</div>
                    <div class="text-gray-400 text-sm">Compte principal</div>
                </div>
                <div class="text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Section des transactions récentes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Historique des Transactions</h2>
            
            <?php if (!empty($transactions)): ?>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2 text-left">Type</th>
                            <th class="border border-gray-300 px-4 py-2 text-right">Montant</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2">
                                    <?= strtoupper($transaction['type']) ?>
                                </td>
                                <td class="border border-gray-300 px-4 py-2 text-right font-mono">
                                    <?= number_format($transaction['montant'], 2, ',', ' ') ?> FCFA
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <?= date('d/m/Y', strtotime($transaction['date'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-500 text-center py-8">Aucune transaction à afficher</p>
            <?php endif; ?>
        </div>
        
    </div>
    
</body>
</html>