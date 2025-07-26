<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu Woyofal - MaxitSa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow no-print">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="<?= APP_URL ?>/woyofal" class="text-blue-600 hover:text-blue-800 mr-4">
                            ← Retour à Woyofal
                        </a>
                        <h1 class="text-xl font-semibold text-gray-900">Reçu d'achat Woyofal</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Imprimer
                        </button>
                        <a href="<?= APP_URL ?>/dashboard" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Reçu -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <!-- En-tête du reçu -->
                <div class="bg-blue-600 text-white px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold">MAXITSA</h2>
                            <p class="text-blue-100">Service de paiement électronique</p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-xl font-semibold">REÇU WOYOFAL</h3>
                            <p class="text-blue-100">Achat d'électricité SENELEC</p>
                        </div>
                    </div>
                </div>

                <!-- Détails du reçu -->
                <div class="px-6 py-6">
                    <!-- Informations de transaction -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Informations de transaction</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Transaction ID :</span>
                                    <span class="font-medium">#<?= htmlspecialchars($transaction_id) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Référence :</span>
                                    <span class="font-medium"><?= htmlspecialchars($recu['reference']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date d'achat :</span>
                                    <span class="font-medium"><?= date('d/m/Y H:i:s', strtotime($recu['date_achat'])) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Montant payé :</span>
                                    <span class="font-bold text-green-600"><?= number_format($recu['montant'], 0, ',', ' ') ?> FCFA</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Informations client</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nom du client :</span>
                                    <span class="font-medium"><?= htmlspecialchars($recu['client_name']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Numéro compteur :</span>
                                    <span class="font-medium"><?= htmlspecialchars($recu['compteur']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date SENELEC :</span>
                                    <span class="font-medium"><?= htmlspecialchars($recu['date_heure']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Code de recharge - Section importante -->
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6 mb-6">
                        <h4 class="text-xl font-bold text-center text-yellow-800 mb-4">CODE DE RECHARGE</h4>
                        <div class="text-center">
                            <div class="text-3xl font-mono font-bold text-yellow-900 bg-white p-4 rounded border-2 border-dashed border-yellow-400 inline-block">
                                <?= htmlspecialchars($recu['code_recharge']) ?>
                            </div>
                        </div>
                        <p class="text-center text-yellow-700 mt-3 text-sm">
                            Utilisez ce code sur votre compteur pour recharger votre électricité
                        </p>
                    </div>

                    <!-- Détails de la recharge -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h5 class="font-semibold text-blue-800 mb-2">Détails de la recharge</h5>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Nombre de kWh :</span>
                                    <span class="font-medium"><?= htmlspecialchars($recu['nombre_kwh']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tranche tarifaire :</span>
                                    <span class="font-medium"><?= htmlspecialchars($recu['tranche']) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Prix unitaire :</span>
                                    <span class="font-medium"><?= htmlspecialchars($recu['prix_unitaire']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <h5 class="font-semibold text-green-800 mb-2">Instructions</h5>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• Tapez *155# sur votre téléphone</li>
                                <li>• Sélectionnez "Recharge électricité"</li>
                                <li>• Entrez le code de recharge</li>
                                <li>• Confirmez la recharge</li>
                                <li>• Conservez ce reçu</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="border-t pt-4 text-center text-gray-500 text-sm">
                        <p>Merci d'avoir utilisé MaxitSa pour votre achat d'électricité Woyofal</p>
                        <p>Service client : support@maxitsa.sn | Tel: +221 XX XXX XX XX</p>
                        <p class="mt-2">Ce reçu fait foi de votre achat d'électricité</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-center space-x-4 no-print">
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                    📄 Imprimer le reçu
                </button>
                <a href="<?= APP_URL ?>/woyofal" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                    🔄 Nouvel achat
                </a>
                <a href="<?= APP_URL ?>/dashboard" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg">
                    🏠 Retour au dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
