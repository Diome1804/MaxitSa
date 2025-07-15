<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MaxitSa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">Dashboard MaxitSa</h1>
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
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Card Transactions -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">T</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Transactions
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            <?= count($transactions ?? []) ?>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Solde -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">€</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Solde Total
                                        </dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            0 FCFA
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Comptes -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold">C</span>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Mes Comptes
                                            </dt>
                                            <dd class="text-lg font-medium text-gray-900">
                                                1
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <!-- Bouton Nouveau compte -->
                                <a href="<?= APP_URL ?>/create" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-xs font-semibold ml-4">
                                    Nouveau compte
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✅ SECTION TRANSACTIONS -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Historique des Transactions</h2>
                        <!-- Lien voir plus -->
                        <a href="<?= APP_URL ?>/transactions" class="text-blue-500 hover:text-blue-700 text-sm font-semibold underline">Voir plus</a>
                    </div>
                    
                    <?php if (!empty($transactions)): ?>
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
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= htmlspecialchars($transaction['type']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= htmlspecialchars($transaction['montant']) ?> FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= htmlspecialchars($transaction['date']) ?>
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
        </main>
    </div>
</body>
</html>