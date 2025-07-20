<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Transactions - MaxitSa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="<?= APP_URL ?>/dashboard" class="text-blue-500 hover:text-blue-700 mr-4">
                            ← Retour au Dashboard
                        </a>
                        <h1 class="text-xl font-semibold text-gray-900">Mes Transactions</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">
                            Bienvenue, <?= htmlspecialchars($user['nom'] ?? 'Utilisateur') ?> <?= htmlspecialchars($user['prenom'] ?? '') ?>
                        </span>
                        <a href="<?= APP_URL ?>/logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                            Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                
                <!-- Messages de succès/erreur -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Filtres -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Filtrer les transactions</h2>
                    <form method="GET" action="<?= APP_URL ?>/transactions" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Filtre par type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de transaction</label>
                            <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                                <option value="">Tous les types</option>
                                <?php foreach ($transactionTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>" <?= (isset($filters['type']) && $filters['type'] === $type) ? 'selected' : '' ?>>
                                        <?= ucfirst($type) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Date de début -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                            <input type="date" name="date_debut" value="<?= htmlspecialchars($filters['date_debut'] ?? '') ?>" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                        </div>

                        <!-- Date de fin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                            <input type="date" name="date_fin" value="<?= htmlspecialchars($filters['date_fin'] ?? '') ?>" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-semibold">
                                Filtrer
                            </button>
                            <a href="<?= APP_URL ?>/transactions" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded font-semibold">
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Liste des transactions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Toutes les transactions
                            <?php if (!empty($filters)): ?>
                                <span class="text-sm font-normal text-gray-600">(filtré)</span>
                            <?php endif; ?>
                        </h2>
                        <div class="text-sm text-gray-600">
                            Total: <?= $pagination['total'] ?? 0 ?> transaction(s)
                        </div>
                    </div>
                    
                    <?php if (!empty($transactions)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Montant
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Compte
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    <?= htmlspecialchars($transaction['type']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold <?= isset($transaction['css_class']) ? $transaction['css_class'] : 'text-gray-900' ?>">
                                                <?= htmlspecialchars($transaction['montant']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                                <?= htmlspecialchars($transaction['date']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">
                                                <?= htmlspecialchars($transaction['numero_compte'] ?? 'N/A') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-lg mb-2">Aucune transaction trouvée</div>
                            <p class="text-gray-500">
                                <?php if (!empty($filters)): ?>
                                    Essayez de modifier vos filtres de recherche.
                                <?php else: ?>
                                    Vous n'avez pas encore effectué de transactions.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Pagination -->
                    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                        <div class="flex justify-center items-center mt-6 space-x-2">
                            <?php
                            // Construire les paramètres de filtre pour les liens de pagination
                            $filterParams = '';
                            if (!empty($filters)) {
                                $filterParams = '&' . http_build_query($filters);
                            }
                            ?>
                            
                            <!-- Bouton Précédent -->
                            <?php if ($pagination['has_prev']): ?>
                                <a href="<?= APP_URL ?>/transactions?page=<?= $pagination['current_page'] - 1 ?><?= $filterParams ?>" 
                                   class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors">
                                    &laquo; Précédent
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-2 text-sm bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                    &laquo; Précédent
                                </span>
                            <?php endif; ?>

                            <!-- Numéros de pages -->
                            <?php 
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <?php if ($i == $pagination['current_page']): ?>
                                    <span class="px-3 py-2 text-sm bg-blue-500 text-white rounded-md">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= APP_URL ?>/transactions?page=<?= $i ?><?= $filterParams ?>" 
                                       class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Bouton Suivant -->
                            <?php if ($pagination['has_next']): ?>
                                <a href="<?= APP_URL ?>/transactions?page=<?= $pagination['current_page'] + 1 ?><?= $filterParams ?>" 
                                   class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors">
                                    Suivant &raquo;
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-2 text-sm bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                    Suivant &raquo;
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Informations sur la pagination -->
                        <div class="text-center mt-3 text-sm text-gray-600">
                            Affichage de <?= ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 ?> à 
                            <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?> 
                            sur <?= $pagination['total'] ?> transactions
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
