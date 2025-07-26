<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Woyofal - Achat d'électricité - MaxitSa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="<?= APP_URL ?>/dashboard" class="text-blue-600 hover:text-blue-800 mr-4">
                            ← Retour au dashboard
                        </a>
                        <h1 class="text-xl font-semibold text-gray-900">Woyofal - Achat d'électricité</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">
                            <?= htmlspecialchars($user['nom'] ?? 'Utilisateur') ?> <?= htmlspecialchars($user['prenom'] ?? '') ?>
                        </span>
                        <a href="<?= APP_URL ?>/logout" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Messages de feedback -->
            <?php if (isset($success) && $success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error) && $error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Formulaire d'achat -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z"/>
                            </svg>
                            Acheter un code Woyofal
                        </h3>
                        <p class="text-yellow-100 text-sm mt-1">Rechargez votre électricité facilement</p>
                    </div>
                    
                    <div class="px-6 py-6">
                        <form action="<?= APP_URL ?>/woyofal/acheter" method="POST" class="space-y-6">
                            <!-- Numéro de compteur -->
                            <div class="space-y-2">
                                <label for="compteur" class="block text-sm font-semibold text-gray-900">
                                    <svg class="w-4 h-4 inline mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                    Numéro de compteur
                                </label>
                                <input type="text" 
                                       id="compteur" 
                                       name="compteur" 
                                       value="<?= htmlspecialchars($old['compteur'] ?? '') ?>"
                                       placeholder="Entrez le numéro de votre compteur"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 text-lg <?= isset($errors['compteur']) ? 'border-red-500 bg-red-50' : '' ?>">
                                <?php if (isset($errors['compteur'])): ?>
                                    <div class="flex items-center mt-2 text-red-600">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm"><?= htmlspecialchars($errors['compteur']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <p class="mt-2 text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Numéro de compteur SENELEC (9 à 11 chiffres)
                                </p>
                            </div>

                            <!-- Montant -->
                            <div class="space-y-2">
                                <label for="montant" class="block text-sm font-semibold text-gray-900">
                                    <svg class="w-4 h-4 inline mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                    Montant à recharger
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           id="montant" 
                                           name="montant" 
                                           value="<?= htmlspecialchars($old['montant'] ?? '') ?>"
                                           placeholder="Ex: 5000"
                                           class="block w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 text-lg <?= isset($errors['montant']) ? 'border-red-500 bg-red-50' : '' ?>">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-medium">FCFA</span>
                                    </div>
                                </div>
                                <?php if (isset($errors['montant'])): ?>
                                    <div class="flex items-center mt-2 text-red-600">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm"><?= htmlspecialchars($errors['montant']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Minimum: 500 FCFA
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293 4.293a1 1 0 011.414 0L9 8.586V16a1 1 0 11-2 0V8.586L5.707 9.879a1 1 0 01-1.414-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L9 8.586V16a1 1 0 11-2 0V8.586l-1.293 1.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Maximum: 500 000 FCFA
                                    </span>
                                </div>
                            </div>

                            <!-- Compteurs de test -->
                            <div class="bg-blue-50 p-4 rounded-md">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">Compteurs de test disponibles :</h4>
                                <div class="flex flex-wrap gap-2">
                                    <?php 
                                    $compteursTest = ['963852741', '147258369', '654987321', '321654987'];
                                    foreach ($compteursTest as $compteurTest): 
                                    ?>
                                        <button type="button" 
                                                onclick="document.getElementById('compteur').value = '<?= $compteurTest ?>'"
                                                class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded">
                                            <?= $compteurTest ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Acheter le code Woyofal
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Informations tarifaires -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Tranches tarifaires SENELEC
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded">
                                <div>
                                    <span class="font-medium text-green-800">Tranche 1 - Social</span>
                                    <br><span class="text-sm text-green-600">0-150 kWh</span>
                                </div>
                                <span class="font-bold text-green-800">91 FCFA/kWh</span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded">
                                <div>
                                    <span class="font-medium text-yellow-800">Tranche 2 - Normal</span>
                                    <br><span class="text-sm text-yellow-600">150-250 kWh</span>
                                </div>
                                <span class="font-bold text-yellow-800">102 FCFA/kWh</span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded">
                                <div>
                                    <span class="font-medium text-orange-800">Tranche 3 - Intermédiaire</span>
                                    <br><span class="text-sm text-orange-600">250-400 kWh</span>
                                </div>
                                <span class="font-bold text-orange-800">116 FCFA/kWh</span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded">
                                <div>
                                    <span class="font-medium text-red-800">Tranche 4 - Élevé</span>
                                    <br><span class="text-sm text-red-600">400+ kWh</span>
                                </div>
                                <span class="font-bold text-red-800">132 FCFA/kWh</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique récent -->
            <?php if (!empty($historique)): ?>
                <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Achats récents
                            </h3>
                            <a href="<?= APP_URL ?>/woyofal/historique" class="text-blue-600 hover:text-blue-800">
                                Voir tout l'historique →
                            </a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compteur</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($historique as $achat): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= date('d/m/Y H:i', strtotime($achat['date'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= htmlspecialchars($achat['compteur']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= number_format($achat['montant'], 0, ',', ' ') ?> FCFA
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $achat['statut'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                    <?= $achat['statut'] === 'success' ? 'Réussi' : 'Échoué' ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php if ($achat['statut'] === 'success'): ?>
                                                    <a href="<?= APP_URL ?>/woyofal/recu?id=<?= $achat['id'] ?>" 
                                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                                        Voir reçu
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Popup de reçu -->
    <?php if (isset($show_receipt_popup) && $show_receipt_popup && isset($recu_data)): ?>
    <div id="receiptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- En-tête -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Reçu d'achat Woyofal</h3>
                    <button onclick="closeReceiptModal()" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Détails de la transaction -->
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="font-medium">Transaction #:</span>
                            <span><?= htmlspecialchars($transaction_id ?? 'N/A') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Client:</span>
                            <span><?= htmlspecialchars($recu_data['client_name'] ?? 'Client Woyofal') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Compteur:</span>
                            <span><?= htmlspecialchars($recu_data['compteur'] ?? '') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Code de recharge:</span>
                            <span class="font-mono bg-yellow-100 px-2 py-1 rounded text-yellow-800">
                                <?= htmlspecialchars($recu_data['code_recharge'] ?? '') ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Énergie:</span>
                            <span><?= htmlspecialchars($recu_data['nombre_kwh'] ?? 0) ?> kWh</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Tranche:</span>
                            <span><?= htmlspecialchars($recu_data['tranche'] ?? '') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Prix unitaire:</span>
                            <span><?= number_format($recu_data['prix_unitaire'] ?? 0, 0, ',', ' ') ?> FCFA/kWh</span>
                        </div>
                    </div>
                </div>

                <!-- Montant total -->
                <div class="bg-blue-50 p-4 rounded-lg mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-blue-800">Total payé:</span>
                        <span class="text-xl font-bold text-blue-800">
                            <?= number_format($recu_data['montant'] ?? 0, 0, ',', ' ') ?> FCFA
                        </span>
                    </div>
                </div>

                <!-- Date et référence -->
                <div class="text-xs text-gray-500 mb-4">
                    <div>Date: <?= date('d/m/Y H:i:s', strtotime($recu_data['date_achat'] ?? 'now')) ?></div>
                    <div>Référence: <?= htmlspecialchars($recu_data['reference'] ?? '') ?></div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex space-x-3">
                    <button onclick="printReceipt()" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded text-sm">
                        Imprimer
                    </button>
                    <button onclick="closeReceiptModal()" 
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded text-sm">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeReceiptModal() {
            document.getElementById('receiptModal').style.display = 'none';
        }

        function printReceipt() {
            // Créer une version imprimable
            const receiptContent = document.querySelector('#receiptModal .relative').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Reçu Woyofal</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .font-bold { font-weight: bold; }
                        .text-center { text-align: center; }
                        .mb-4 { margin-bottom: 1rem; }
                        .space-y-2 > * + * { margin-top: 0.5rem; }
                        .flex { display: flex; }
                        .justify-between { justify-content: space-between; }
                        .bg-gray-50, .bg-blue-50, .bg-yellow-100 { background: #f9f9f9; padding: 10px; margin: 5px 0; }
                    </style>
                </head>
                <body>${receiptContent}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        // Auto-fermer le modal si on clique en dehors
        document.getElementById('receiptModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeReceiptModal();
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
