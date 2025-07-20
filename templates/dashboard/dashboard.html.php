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
                
                <!-- Messages de succès/erreur -->
                <?php
                use App\Core\Session;
                if (Session::isset('success')): ?>
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <?= htmlspecialchars(Session::get('success')) ?>
                    </div>
                    <?php Session::unset('success'); ?>
                <?php endif; ?>

                <?php if (Session::isset('error')): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <?= htmlspecialchars(Session::get('error')) ?>
                    </div>
                    <?php Session::unset('error'); ?>
                <?php endif; ?>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 overflow-visible">
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
                                            <?= number_format($solde ?? 0, 0, ',', ' ') ?> FCFA
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Comptes -->
                    <div class="bg-white overflow-visible overflow-x-visible overflow-y-visible shadow rounded-lg">
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
                                            <!-- <dd class="text-lg font-medium text-gray-900">
                                                <?= isset($comptes) ? count($comptes) : 1 ?>
                                            </dd> -->
                                        </dl>
                                    </div>
                                </div>
                                <!-- Liste déroulante des comptes -->
                                <div class="relative ml-4 overflow-visible z-50">
                                    <button id="dropdownCompteBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded text-xs font-semibold flex items-center">
                                        Voir mes comptes
                                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div id="dropdownCompteList" class="absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded shadow-lg z-[9999] hidden">
                                        <div class="py-2">
                                            <?php if (!empty($comptes)): ?>
                                                <?php foreach ($comptes as $compte): ?>
                                                    <div class="px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100 last:border-b-0">
                                                        <div class="flex justify-between items-center">
                                                            <div class="flex flex-col">
                                                                <span class="font-semibold text-gray-900"><?= htmlspecialchars($compte['num_compte'] ?? 'N/A') ?></span>
                                                                <span class="text-xs text-gray-500">Solde: <?= number_format($compte['solde'] ?? 0, 0, ',', ' ') ?> FCFA</span>
                                                            </div>
                                                            <div class="flex items-center space-x-2">
                                                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= ($compte['type'] ?? '') === 'ComptePrincipal' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' ?>">
                                                                    <?= ($compte['type'] ?? '') === 'ComptePrincipal' ? 'Principal' : 'Secondaire' ?>
                                                                </span>
                                                                <?php if (($compte['type'] ?? '') === 'CompteSecondaire'): ?>
                                                                    <form method="POST" action="<?= APP_URL ?>/changer-principal" class="inline">
                                                                        <input type="hidden" name="compte_id" value="<?= $compte['id'] ?>">
                                                                        <button type="submit" class="text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded" onclick="return confirm('Êtes-vous sûr de vouloir faire de ce compte votre compte principal ?')">
                                                                            Définir comme principal
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="px-4 py-2 text-sm text-gray-400">Aucun compte</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Bouton Nouveau compte -->
                                <button id="openCompteModal" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-xs font-semibold ml-4">
                                    Nouveau compte
                                </button>
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

                    <!-- Pagination -->
                    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                        <div class="flex justify-center items-center mt-6 space-x-2">
                            <!-- Bouton Précédent -->
                            <?php if ($pagination['has_prev']): ?>
                                <a href="<?= APP_URL ?>/dashboard?page=<?= $pagination['current_page'] - 1 ?>" 
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
                                    <a href="<?= APP_URL ?>/dashboard?page=<?= $i ?>" 
                                       class="px-3 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-colors">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Bouton Suivant -->
                            <?php if ($pagination['has_next']): ?>
                                <a href="<?= APP_URL ?>/dashboard?page=<?= $pagination['current_page'] + 1 ?>" 
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

    <!-- Modal Nouveau Compte -->
    <div id="compteModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Créer un compte secondaire</h3>
            <form id="formNouveauCompte" method="POST" action="/create">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-2">Numéro téléphone</label>
                    <input type="text" name="telephone" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500" placeholder="Numéro téléphone">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-2">Solde initial</label>
                    <input type="number" name="solde" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500" placeholder="Solde initial">
                </div>
                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" id="closeCompteModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Annuler</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Ouvre le modal
        document.getElementById('openCompteModal').onclick = function() {
            document.getElementById('compteModal').classList.remove('hidden');
        };
        // Ferme le modal
        document.getElementById('closeCompteModal').onclick = function() {
            document.getElementById('compteModal').classList.add('hidden');
        };
        // Fermer le modal si on clique en dehors
        document.getElementById('compteModal').onclick = function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        };

        // Dropdown comptes
        const dropdownBtn = document.getElementById('dropdownCompteBtn');
        const dropdownList = document.getElementById('dropdownCompteList');
        dropdownBtn.onclick = function(e) {
            e.stopPropagation();
            dropdownList.classList.toggle('hidden');
        };
        document.addEventListener('click', function() {
            dropdownList.classList.add('hidden');
        });
    </script>
</body>
</html>