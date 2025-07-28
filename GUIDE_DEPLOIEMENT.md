# Guide de Déploiement MAXITSA sur Render

## 📋 Pré-requis

1. **Compte Render** : [https://render.com](https://render.com)
2. **Repository GitHub** : Code source sur GitHub
3. **Fichiers de configuration** : ✅ Tous présents dans le projet

## 🔧 Fichiers de Configuration

### ✅ Fichiers créés et configurés :

- **`render.yaml`** - Configuration des services Render
- **`Dockerfile`** - Configuration du container PHP
- **`deploy.php`** - Script de migrations pour le déploiement
- **`.env.production`** - Variables d'environnement pour la production
- **`app/config/env.php`** - Gestion des variables d'environnement

## 🚀 Étapes de Déploiement

### 1. Préparation du Repository

```bash
# Vérifier que tous les fichiers sont commitées
git add .
git commit -m "Configuration déploiement Render"
git push origin main
```

### 2. Création des Services sur Render

#### A. Base de Données PostgreSQL

1. **Aller sur Render Dashboard** → New → PostgreSQL
2. **Configuration** :
   - Name: `maxitsa-db`
   - Plan: `Free`
   - Database Name: `maxitsa`
   - Username: `maxitsa_user`

#### B. Application Web

1. **Aller sur Render Dashboard** → New → Web Service
2. **Configuration** :
   - Repository: Connecter votre repo GitHub
   - Name: `maxitsa-app`
   - Environment: `Docker`
   - Plan: `Free`

### 3. Variables d'Environnement

Render va automatiquement créer ces variables grâce au `render.yaml` :

- `DB_HOST` - Host de la base de données
- `DB_PORT` - Port de la base de données  
- `DB_NAME` - Nom de la base de données
- `DB_USER` - Utilisateur de la base de données
- `DB_PASSWORD` - Mot de passe de la base de données
- `APP_URL` - URL de votre application
- `APPDAF_API_URL` - URL de l'API APPDAF
- `WOYOFAL_API_URL` - URL de l'API Woyofal

### 4. Processus de Déploiement Automatique

Render va automatiquement :

1. **Build** l'image Docker
2. **Installer** les dépendances Composer
3. **Exécuter** les migrations de base de données
4. **Démarrer** le serveur PHP

## 🔍 Vérification du Déploiement

### URLs de Test

- **Application** : `https://maxitsa-app.onrender.com`
- **Page de connexion** : `https://maxitsa-app.onrender.com/login`
- **API Status** : `https://maxitsa-app.onrender.com/api/status`

### Tests à Effectuer

1. **Connexion utilisateur**
2. **Navigation dans le dashboard**
3. **Test des transactions** :
   - Dépôt d'argent
   - Transfert entre comptes
   - Achat Woyofal
4. **Test des APIs externes** :
   - Integration APPDAF
   - Integration Woyofal

## 🛠 Debugging

### Logs de Déploiement

Render fournit des logs en temps réel :
- Build logs : Pendant la construction
- Deploy logs : Pendant le déploiement
- Runtime logs : Pendant l'exécution

### Commandes de Debug

```bash
# Test local des migrations
php deploy.php

# Test local de la configuration
php test_config.php

# Vérification de la base de données
php test_transactions.php
```

## 🔄 Redéploiement

Pour redéployer après des modifications :

1. **Commit** les changements
2. **Push** sur GitHub
3. **Render** redéploie automatiquement

## 📊 Monitoring

- **Render Dashboard** : Métriques de performance
- **Database Metrics** : Utilisation, connexions
- **Application Logs** : Erreurs et activité

## 🆘 Support

En cas de problème :

1. **Vérifier les logs** sur Render Dashboard
2. **Tester localement** avec `php test_config.php`
3. **Vérifier les variables d'environnement**
4. **Contacter le support** Render si nécessaire

## ✅ Checklist de Déploiement

- [ ] Repository GitHub à jour
- [ ] Base de données PostgreSQL créée
- [ ] Application web configurée  
- [ ] Variables d'environnement définies
- [ ] Premier déploiement réussi
- [ ] Tests de fonctionnalité effectués
- [ ] URLs de production configurées

---

🎉 **Votre application MAXITSA est maintenant prête pour la production !**
