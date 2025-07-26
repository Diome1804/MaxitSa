# Intégration API Woyofal - MaxitSa

## 🎯 User Story Implémentée

**US1** : En tant que client je peux acheter un code woyofal à partir de mon compte principal sur MAXITSA.

### Critères d'Acceptation ✅
- [x] Numéro compteur et montant obligatoires
- [x] Vérification de la disponibilité du montant dans le compte principal  
- [x] Génération d'un reçu une fois la transaction réussie
- [x] Informations du reçu : Nom client, Numéro compteur, Code de recharge, Date/Heure, Tranche et prix unitaire

## 🏗️ Architecture Respectant SOLID

### 1. Interface (I - Interface Segregation)
- **[`WoyofalServiceInterface`](file:///home/lex_code/Documents/PHP_POO/MAXITSA/app/core/interfaces/WoyofalServiceInterface.php)** - Contrat du service Woyofal

### 2. Service (S - Single Responsibility)
- **[`WoyofalService`](file:///home/lex_code/Documents/PHP_POO/MAXITSA/src/service/WoyofalService.php)** - Logique métier Woyofal
  - Validation des données
  - Appel API avec retry et backoff exponentiel  
  - Gestion des erreurs
  - Débit du compte
  - Génération de reçu

### 3. Contrôleur (O - Open/Closed)
- **[`WoyofalController`](file:///home/lex_code/Documents/PHP_POO/MAXITSA/src/controller/WoyofalController.php)** - Interface utilisateur
  - Injection de dépendances
  - Validation formulaire
  - Gestion des sessions

### 4. Configuration DI (D - Dependency Inversion)
- **[`services.yml`](file:///home/lex_code/Documents/PHP_POO/MAXITSA/app/config/services.yml)** - Configuration des services
- **[`Router.php`](file:///home/lex_code/Documents/PHP_POO/MAXITSA/app/core/Router.php)** - Mapping des contrôleurs

## 🌐 Routes Créées

```
/woyofal              GET    - Page d'achat Woyofal
/woyofal/acheter      POST   - Traitement de l'achat  
/woyofal/recu         GET    - Affichage du reçu
/woyofal/historique   GET    - Historique des achats
```

## 🎨 Interfaces Utilisateur

### 1. Page d'achat
- **[`templates/woyofal/index.html.php`](file:///home/lex_code/Documents/PHP_POO/MAXITSA/templates/woyofal/index.html.php)**
  - Formulaire de saisie (compteur + montant)
  - Compteurs de test préremplis
  - Tranches tarifaires SENELEC
  - Historique récent des achats

### 2. Reçu d'achat  
- **[`templates/woyofal/recu.html.php`](file:///home/lex_code/Documents/PHP_POO/MAXITSA/templates/woyofal/recu.html.php)**
  - Code de recharge mis en évidence
  - Informations client et transaction
  - Détails de la recharge (kWh, tranche, prix)
  - Instructions d'utilisation
  - Bouton d'impression

### 3. Dashboard enrichi
- **[`templates/dashboard/dashboard.html.php`](file:///home/lex_code/Documents/PHP_POO/MAXITSA/templates/dashboard/dashboard.html.php)**
  - Section "Actions rapides" ajoutée
  - Lien vers Woyofal avec icône électricité

## 🔧 Fonctionnalités Techniques

### Validation Robuste
- Compteur : 9-11 chiffres
- Montant : 500 - 500 000 FCFA
- Solde : Vérification avant achat

### Appel API Sécurisé
- Timeout 30 secondes
- Retry 3 fois avec backoff exponentiel
- Gestion d'erreurs complète
- Headers appropriés

### Gestion des Transactions
- Débit automatique du compte principal
- Enregistrement en base de données
- Historique des achats
- Statuts de transaction

### Interface Utilisateur
- Design responsive (Tailwind CSS)
- Messages de feedback clairs
- Compteurs de test pour développement
- Impression du reçu

## 📊 Données API Woyofal

### Tranches Tarifaires Intégrées
- **Tranche 1 - Social** : 0-150 kWh à 91 FCFA/kWh
- **Tranche 2 - Normal** : 150-250 kWh à 102 FCFA/kWh  
- **Tranche 3 - Intermédiaire** : 250-400 kWh à 116 FCFA/kWh
- **Tranche 4 - Élevé** : 400+ kWh à 132 FCFA/kWh

### Mapping Reçu MaxitSa
- Nom client → `data.client`
- Numéro compteur → `data.compteur`  
- Code de recharge → `data.code`
- Nombre KW → `data.nbreKwt`
- Date/Heure → `data.date`
- Tranche → `data.tranche`
- Prix unitaire → `data.prix`
- Référence → `data.reference`

## 🔄 Flow d'Achat Implémenté

1. **Validation** - Compteur et montant
2. **Vérification** - Solde disponible
3. **Appel API** - Woyofal avec retry
4. **Débit** - Compte principal
5. **Enregistrement** - Transaction en base
6. **Reçu** - Génération et affichage

## ✅ Tests Possibles

### Compteurs de Test Configurés
- `123456789`, `987654321`, `456789123`
- `789123456`, `345678912`

### Scenarios de Test
- Achat normal (solde suffisant)
- Solde insuffisant
- Compteur invalide  
- Montant hors limites
- API indisponible

## 🚀 Déploiement

L'intégration est **prête pour utilisation** :
- Code respectant SOLID ✅
- Injection de dépendances ✅  
- Gestion d'erreurs robuste ✅
- Interface utilisateur complète ✅
- Documentation incluse ✅

## 📝 Notes Techniques

- **ReflectionFactory** utilisée pour l'instanciation
- **Middleware auth** protège les routes
- **Sessions** pour les messages flash
- **Responsive design** mobile-friendly
- **Logs d'erreur** pour le debugging
