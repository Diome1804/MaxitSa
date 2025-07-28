# 🚂 Configuration Railway PostgreSQL pour MAXITSA

## 📋 Architecture

- **Application** : Render (https://maxitsa-3ab3.onrender.com)
- **Base de données** : Railway PostgreSQL

## 🚀 Étapes de Configuration

### 1. Créer la Base de Données sur Railway

1. **Aller sur [Railway](https://railway.app)**
2. **Créer un nouveau projet**
3. **Ajouter PostgreSQL** : 
   - Click "Add service"
   - Select "PostgreSQL"
   - Attendre la création

### 2. Récupérer l'URL de Connexion

Dans Railway PostgreSQL service :
- **Variables** → `DATABASE_URL`
- Copier l'URL (format : `postgresql://user:password@host:port/database`)

### 3. Configurer Render avec Railway DB

Dans **Render Dashboard** → **maxitsa-app** → **Environment** :

Ajouter/Modifier :
```
DATABASE_URL = [URL_RAILWAY_POSTGRESQL]
```

### 4. Exécuter les Migrations

Une fois configuré, lancer :
```bash
php setup_railway_db.php
```

## 🔧 Variables d'Environnement

### Sur Render (Application)
- `DATABASE_URL` : URL PostgreSQL de Railway
- `APP_URL` : URL de votre app Render
- `APPDAF_API_URL` : https://appdaff-zwqf.onrender.com
- `WOYOFAL_API_URL` : https://appwoyofal.onrender.com

### Sur Railway (Base de Données)
- Généré automatiquement : `DATABASE_URL`

## 📊 Avantages de cette Configuration

✅ **Séparation des services** : App et DB indépendants  
✅ **Performance Railway** : Base de données optimisée  
✅ **Simplicité Render** : Déploiement app facile  
✅ **Flexibilité** : Peut changer un service sans affecter l'autre  

## 🔍 Test de la Configuration

1. **Render App** : https://maxitsa-3ab3.onrender.com
2. **Test login** avec utilisateurs créés par le seeder
3. **Vérifier fonctionnalités** : dépôts, transferts, Woyofal

## 🆘 Dépannage

- **Erreur connexion** : Vérifier DATABASE_URL sur Render
- **Tables manquantes** : Exécuter `setup_railway_db.php`
- **Données manquantes** : Re-exécuter le seeder

---

🎯 **Configuration hybride Render + Railway pour performances optimales !**
