<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dépôts et Transferts - MaxitSa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                        <h1 class="text-xl font-semibold text-gray-900">Dépôts et Transferts</h1>
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
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error) && $error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Formulaire de dépôt -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-plus-circle text-green-500 mr-2"></i>
                            Effectuer un dépôt
                        </h3>
                        
                        <form method="POST" action="<?= APP_URL ?>/depot/effectuer" class="space-y-4">
                            <!-- Sélection du compte -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Compte destinataire
                                </label>
                                <select name="compte_id" required 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionnez un compte</option>
                                    <?php foreach ($comptes as $compte): ?>
                                        <option value="<?= $compte['id'] ?>" 
                                                <?= (isset($old['compte_id']) && $old['compte_id'] == $compte['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($compte['num_compte']) ?> 
                                            (<?= htmlspecialchars($compte['type']) ?>) - 
                                            <?= number_format($compte['solde'], 0, ',', ' ') ?> FCFA
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['compte_id'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['compte_id']) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Montant -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant à déposer (FCFA)
                                </label>
                                <input type="number" name="montant" required min="1" step="1"
                                       value="<?= htmlspecialchars($old['montant'] ?? '') ?>"
                                       placeholder="Ex: 50000"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <?php if (isset($errors['montant'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['montant']) ?></p>
                                <?php endif; ?>
                            </div>

                            <button type="submit" 
                                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Effectuer le dépôt
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Formulaire de transfert -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-exchange-alt text-blue-500 mr-2"></i>
                            Effectuer un transfert
                        </h3>
                        
                        <form method="POST" action="<?= APP_URL ?>/depot/transfert" class="space-y-4" id="transfertForm">
                            <!-- Compte source -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Compte source
                                </label>
                                <select name="compte_source_id" required id="compteSource"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionnez le compte source</option>
                                    <?php foreach ($comptes as $compte): ?>
                                        <option value="<?= $compte['id'] ?>" data-type="<?= $compte['type'] ?>"
                                                <?= (isset($old['compte_source_id']) && $old['compte_source_id'] == $compte['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($compte['num_compte']) ?> 
                                            (<?= htmlspecialchars($compte['type']) ?>) - 
                                            <?= number_format($compte['solde'], 0, ',', ' ') ?> FCFA
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['compte_source_id'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['compte_source_id']) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Compte destination -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Compte destination
                                </label>
                                <select name="compte_destination_id" required id="compteDestination"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sélectionnez le compte destination</option>
                                    <?php foreach ($comptes as $compte): ?>
                                        <option value="<?= $compte['id'] ?>" data-type="<?= $compte['type'] ?>"
                                                <?= (isset($old['compte_destination_id']) && $old['compte_destination_id'] == $compte['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($compte['num_compte']) ?> 
                                            (<?= htmlspecialchars($compte['type']) ?>) - 
                                            <?= number_format($compte['solde'], 0, ',', ' ') ?> FCFA
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['compte_destination_id'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['compte_destination_id']) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Montant transfert -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant à transférer (FCFA)
                                </label>
                                <input type="number" name="montant_transfert" required min="1" step="1" id="montantTransfert"
                                       value="<?= htmlspecialchars($old['montant_transfert'] ?? '') ?>"
                                       placeholder="Ex: 100000"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <?php if (isset($errors['montant_transfert'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['montant_transfert']) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Affichage des frais -->
                            <div id="fraisInfo" class="bg-yellow-50 border border-yellow-200 rounded-md p-3 hidden">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <span id="fraisText"></span>
                                </p>
                            </div>

                            <button type="submit" 
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Effectuer le transfert
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Transactions annulables -->
            <?php if (!empty($transactions_annulables)): ?>
                <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <i class="fas fa-undo text-orange-500 mr-2"></i>
                            Transactions annulables
                        </h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compte</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($transactions_annulables as $transaction): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= date('d/m/Y H:i', strtotime($transaction['date'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= ucfirst($transaction['type']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= number_format($transaction['montant'], 0, ',', ' ') ?> FCFA
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= htmlspecialchars($transaction['num_compte'] ?? 'N/A') ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <form method="POST" action="<?= APP_URL ?>/depot/annuler" class="inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette transaction ?')">
                                                    <input type="hidden" name="transaction_id" value="<?= $transaction['id'] ?>">
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 font-medium">
                                                        <i class="fas fa-undo mr-1"></i>
                                                        Annuler
                                                    </button>
                                                </form>
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

    <script>
        // Calcul automatique des frais de transfert
        document.addEventListener('DOMContentLoaded', function() {
            const compteSource = document.getElementById('compteSource');
            const compteDestination = document.getElementById('compteDestination');
            const montantTransfert = document.getElementById('montantTransfert');
            const fraisInfo = document.getElementById('fraisInfo');
            const fraisText = document.getElementById('fraisText');

            function calculerFrais() {
                const sourceId = compteSource.value;
                const destinationId = compteDestination.value;
                const montant = parseFloat(montantTransfert.value);

                if (sourceId && destinationId && montant > 0) {
                    const sourceType = compteSource.options[compteSource.selectedIndex].dataset.type;
                    const destinationType = compteDestination.options[compteDestination.selectedIndex].dataset.type;

                    // Calcul des frais (0.08% du montant, plafonné à 5000 FCFA pour transferts entre comptes principaux)
                    if (sourceType === 'ComptePrincipal' && destinationType === 'ComptePrincipal') {
                        let frais = montant * 0.0008; // 0.08%
                        frais = Math.min(frais, 5000); // Plafond de 5000 FCFA
                        const montantTotal = montant + frais;

                        fraisText.innerHTML = `Frais de transfert: ${frais.toLocaleString('fr-FR')} FCFA<br>
                                               Montant total à débiter: ${montantTotal.toLocaleString('fr-FR')} FCFA`;
                        fraisInfo.classList.remove('hidden');
                    } else {
                        fraisInfo.classList.add('hidden');
                    }
                } else {
                    fraisInfo.classList.add('hidden');
                }
            }

            compteSource.addEventListener('change', calculerFrais);
            compteDestination.addEventListener('change', calculerFrais);
            montantTransfert.addEventListener('input', calculerFrais);

            // Empêcher la sélection du même compte source et destination
            compteSource.addEventListener('change', function() {
                const sourceValue = this.value;
                Array.from(compteDestination.options).forEach(option => {
                    if (option.value === sourceValue) {
                        option.disabled = true;
                        if (compteDestination.value === sourceValue) {
                            compteDestination.value = '';
                        }
                    } else {
                        option.disabled = false;
                    }
                });
                calculerFrais();
            });

            compteDestination.addEventListener('change', function() {
                const destinationValue = this.value;
                Array.from(compteSource.options).forEach(option => {
                    if (option.value === destinationValue) {
                        option.disabled = true;
                        if (compteSource.value === destinationValue) {
                            compteSource.value = '';
                        }
                    } else {
                        option.disabled = false;
                    }
                });
                calculerFrais();
            });
        });
    </script>
</body>
</html>
