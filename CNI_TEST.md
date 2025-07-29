# 📋 CNI DE TEST POUR MAXITSA

## 🏦 **Utilisateurs existants dans MaxitSA (pour connexion)**
Ces CNI sont déjà dans la base MaxitSA, utilisez-les pour vous connecter :

| Nom | Téléphone | Mot de passe | CNI |
|-----|-----------|-------------|-----|
| Fallou Ndiaye | 778232295 | passer123 | 9876543210987 |
| Abdou Diallo | 771234567 | Dakar2026 | 9876543210988 |
| Aminata Fall | 785432198 | aminata123 | 9876543210989 |
| Ousmane Ba | 776543210 | ousmane2024 | 9876543210990 |
| Fatou Seck | 704567891 | fatou456 | 9876543210991 |

## ✅ **CNI pour tester l'inscription**
Ces CNI existent dans l'API AppDAF mais PAS dans MaxitSA, utilisez-les pour tester l'inscription :

| CNI | Nom dans AppDAF | Utilisation |
|-----|----------------|-------------|
| **1234567890123** | Dion | ✅ Pour tester une inscription réussie |
| **1234567890124** | (à vérifier) | ✅ Pour tester une autre inscription |
| **1234567890125** | (à vérifier) | ✅ Pour tester une autre inscription |

## ❌ **CNI qui ne fonctionnent pas**
Ces CNI n'existent ni dans AppDAF ni dans MaxitSA :
- 1111111111111 (test d'échec)
- 0000000000000 (test d'échec)

## 🧪 **Comment tester :**

1. **Connexion :** Utilisez un compte existant ci-dessus
2. **Inscription nouvelle :** Utilisez un CNI qui existe dans AppDAF (1234567890123)
3. **Inscription échouée :** Utilisez un CNI inexistant (1111111111111)

## 🔍 **Vérification :**
Vous pouvez tester l'API AppDAF avec :
```bash
php test_appdaf_api.php
```
