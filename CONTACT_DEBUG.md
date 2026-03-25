# 🔧 Guide Débogage — Formulaire de Contact TechBurk

## ❌ Le formulaire ne fonctionne pas ?

Suivez ce guide étape par étape pour diagnostiquer le problème.

---

## 📋 Étape 1 : Vérifier que XAMPP est lancé

### Sur Windows
1. Ouvrir **XAMPP Control Panel**
2. Vérifier que **Apache** est ✅ **Started** (vert)
3. Vérifier que **MySQL** est ✅ **Started** (vert)

```
Apache   | Start | Stop | Admin | [ ] Autostart
MySQL    | Start | Stop | Admin | [ ] Autostart
```

Si ce n'est pas le cas, cliquez sur **"Start"** pour chaque service.

---

## 🔍 Étape 2 : Lancer la page de diagnostic

### Dans votre navigateur, allez à :
```
http://localhost/techburk/diagnostic.php
```

Cette page vous montrera :
- ✅ Si PHP fonctionne
- ✅ Si la base de données est accessible
- ✅ L'état de tous les fichiers
- ✅ Vous permet de tester l'envoi

### Que chercher dans le diagnostic

**✓ SUCCÈS (vert)**  
Si vous voyez : "Connexion à la BD réussie!" c'est bon.

**✗ ERREUR (rouge)**  
Si vous voyez une erreur de connexion →

---

## 🚨 Résoudre les erreurs courantes

### Erreur 1: "Impossible de se connecter à la base de données"

**Cause**: MySQL ne démarre pas

**Solution**:
1. Ouvrir **XAMPP Control Panel**
2. Cliquer sur **"Start"** pour MySQL
3. Attendre 5 secondes
4. Rafraîchir la page `diagnostic.php`

---

### Erreur 2: "Base de données 'techburk_db' n'existe pas"

**Cause**: La base n'a pas été créée

**Solution 1 - Automatique (nouveau)**:
- La table se crée automatiquement lors du premier envoi
- Envoyez simplement le formulaire et elle sera créée

**Solution 2 - Manuel (via phpMyAdmin)**:
1. Aller à `http://localhost/phpmyadmin`
2. En haut, cliquer sur "Importer"
3. Upload du fichier `database.sql`
4. Cliquer "Exécuter"

---

### Erreur 3: "Le formulaire n'envoie rien"

**Vérifications**:

1. **Ouvrir la console du navigateur** (F12)
2. Cliquer sur l'onglet **"Console"**
3. Remplir et envoyer le formulaire
4. Voir les erreurs JavaScript

**Erreur courante**:
```
POST http://localhost/techburk/php/contact.php 404
```

**Solution**: Vérifier que le fichier existe à la bonne location

---

## 🧪 Étape 3 : Tester avec la page simple

Allez à :
```
http://localhost/techburk/test-contact.html
```

Cette page fourni :
- Un formulaire rempli avec des données de test
- Affichage clair des succès/erreurs
- Console pour voir les détails

**Mode d'emploi**:
1. Cliquer sur "Envoyer le test"
2. Attendre la réponse
3. Vérifier le message (succès ou erreur)

---

## 📧 Étape 4 : Vérifier les emails

### Vérifier que les emails sont enregistrés

1. Aller à `http://localhost/phpmyadmin`
2. Cliquer sur la BD **"techburk_db"** (à gauche)
3. Cliquer sur la table **"messages_contact"**
4. Vous devez voir les messages envoyés

**Si vous voyez les messages**: ✅ La base de données fonctionne!

### Vérifier les logs d'email

1. Aller au dossier du projet
2. Ouvrir `logs/emails.log`
3. Voir l'historique : `SENT` ou `FAILED`

**Notes**:
- Sur XAMPP/développement : Emails peut montrer "FAILED" (normal, pas de SMTP)
- Sur un vrai serveur : Affichera "SENT"

---

## 🆘 Problèmes avancés

### Le formulaire du site principal ne fonctionne pas

**Vérifier**:
1. Ouvrir `index.html` dans le navigateur
2. Scroller jusqu'à la section "Contact"
3. Ouvrir la **Console (F12 → Console)**
4. Remplir et envoyer le formulaire
5. Voir les erreurs

**Erreur commune**:
```
CORS error: Cross-origin request blocked
```

**Solution**: 
- C'est normal si vous testez via `file://`
- Utilisez un serveur local : `http://localhost/techburk/`

---

### Plugin/Extension bloque les requêtes

**Solutions**:
1. Désactiver les bloqueurs de pub (uBlock, etc.)
2. Désactiver les VPN/Proxy
3. Essayer dans une fenêtre Privée/Incognito

---

## ✅ Checklist de vérification

- [ ] XAMPP → Apache démarré
- [ ] XAMPP → MySQL démarré
- [ ] `http://localhost/techburk/diagnostic.php` → Base OK
- [ ] `http://localhost/phpmyadmin` → Voir les messages
- [ ] `http://localhost/techburk/test-contact.html` → Envoi réussit
- [ ] `logs/emails.log` → Emails tracés

---

## 🎯 Étapes finales

Une fois que tout fonctionne localement :

1. Héberger le site sur un serveur (InfinityFree, Hostinger, etc.)
2. Adapter les identifiants de base de données
3. Les emails s'enverront automatiquement

---

## 📞 Questions fréquentes

**Q: Où sont stockés les messages?**  
R: Dans la table `messages_contact` de phpMyAdmin

**Q: Comment envoyer les messages par email?**  
R: Automatiquement si votre hébergeur supporte mail()

**Q: Peut-on modifier l'email destinataire?**  
R: Oui, dans `php/contact.php` ligne 17:
```php
define('ADMIN_EMAIL', 'votre@email.com');
```

**Q: Le formulaire demande un honeypot?**  
R: Non, c'est une protection anti-bot invisible

---

*Mise à jour: 25 décembre 2025*
