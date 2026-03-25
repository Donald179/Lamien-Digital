# 📧 Guide Complet — Recevoir les messages sur Gmail

## 🎯 Objectif
Configurer votre site TechBurk pour que les messages du formulaire de contact vous soient envoyés par email via Gmail SMTP.

---

## ⚠️ Pré-requis

1. **Compte Gmail** : lamiendonaldo179@gmail.com (ou votre Gmail)
2. **Vérification 2FA activée** sur votre compte Google
3. **XAMPP lancé** (Apache + MySQL)

---

## 📍 Étape 1 : Générer un App Password

### Qu'est-ce qu'un App Password ?
C'est un mot de passe spécial (16 caractères) généré par Google pour que votre site puisse envoyer des emails via votre compte Gmail.

### Comment le générer :

1. **Aller à** : https://myaccount.google.com/
2. **Cliquer** sur **"Sécurité"** (menu de gauche)
3. **Chercher** : "Mots de passe des applications"
   - Si vous ne voyez pas cette option → Activez la "Vérification en 2 étapes" d'abord
4. **Sélectionner** dans la fenêtre :
   - App: **Mail**
   - Appareil: **Windows** (ou votre OS)
5. **Cliquer** sur **"Générer"**
6. **Copier** le mot de passe généré (16 caractères, sans espaces)

**Exemple** : `xyzabcdefghijklm`

---

## ⚙️ Étape 2 : Configurer config.php

### Ouvrez le fichier `php/config.php`

Cherchez ces lignes :
```php
define('SMTP_USER', 'lamiendonaldo179@gmail.com');
define('SMTP_PASS', '');  // À REMPLIR
```

### Remplissez SMTP_PASS

```php
define('SMTP_USER', 'lamiendonaldo179@gmail.com');
define('SMTP_PASS', 'xyzabcdefghijklm');  // ← Collez votre App Password ici
```

**Important** : 
- ❌ Ne mettez PAS votre mot de passe habituel
- ✅ Mettez le mot de passe généré par Google
- ❌ N'ajoutez PAS d'espaces

### Sauvegarder le fichier

---

## 🧪 Étape 3 : Tester la configuration

### Aller à la page de test :
```
http://localhost/techburk/gmail-setup.php
```

### Tester l'envoi :
1. Cliquer sur **"Envoyer un email de test"**
2. Attendre la réponse
3. Vérifier votre boîte mail Gmail

**Si ✅ Succès** → Vous recevrez un email de test  
**Si ❌ Erreur** → Voir la section "Déboguer"

---

## 🔄 Étape 4 : Mettre à jour index.html

### Modifier le formulaire principal

**Fichier** : `index.html`  
**Cherchez** (ligne ~620) :
```html
<form id="contactForm" action="php/contact.php" method="POST">
```

**Remplacez par** :
```html
<form id="contactForm" action="php/contact-v2.php" method="POST">
```

**Sauvegarder**

---

## ✅ Vérifier que ça marche

### Tester le nouveau formulaire :

1. Aller à `http://localhost/techburk/` 
2. Scroller jusqu'à la section "Contact"
3. Remplir et envoyer le formulaire
4. Vérifier que vous recevez un email dans votre Gmail

**Exemple d'email reçu** :
```
De: noreply@techburk.bf
À: lamiendonaldo179@gmail.com
Sujet: [TechBurk] Nouveau message de contact #1 — Diagnostic informatique

NOUVEAU MESSAGE DE CONTACT — TechBurk
=====================================

Nom          : Jean Dupont
Téléphone    : +226 50 12 34 56
Email        : jean@example.com
Service      : Diagnostic informatique
Date         : 25/03/2026 14:30:45

MESSAGE :
Mon ordinateur ne démarre plus...

=====================================
ID message   : #1
IP client    : 192.168.1.100
Site         : LAMIEN DIGITAL
```

---

## 🔍 Vérifier les messages en BD

### Via phpMyAdmin :

1. Aller à `http://localhost/phpmyadmin`
2. Sélectionner la BD **"techburk_db"**
3. Sélectionner la table **"messages_contact"**
4. Vous verrez tous les messages reçus

### Via requête SQL :
```sql
SELECT * FROM messages_contact ORDER BY date_envoi DESC;
```

---

## 🚨 Déboguer si ça ne marche pas

### Erreur 1 : "Authentification échouée"

**Cause possible** : App Password incorrect  
**Solution** :
1. Vérifier que vous avez copié les 16 caractères sans espaces
2. Regénérer un nouveau App Password
3. Vérifier que 2FA est activé

### Erreur 2 : "Connection refused"

**Cause possible** : Problème de connexion SMTP  
**Solution** :
1. Vérifier votre connexion Internet
2. Certains réseaux bloquent le port 587
3. Essayer sur un autre réseau

### Erreur 3 : "Pas d'email reçu"

**Vérifications** :
1. Vérifier le dossier "Spam" de Gmail 
2. Vérifier les logs : `logs/emails.log`
3. S'assurer que `contact-v2.php` est utilisé

### Voir les logs

Fichier : `logs/emails.log`

**Contenu** :
```
[2026-03-25 14:30:45] SENT - ID#1 - Jean Dupont (+226 50 12 34 56)
[2026-03-25 14:31:12] FAILED - ID#2 - Marie Traoré (+226 60 98 76 54)
```

**Statuts** :
- `SENT` = Email envoyé ✅
- `FAILED` = Email non envoyé ❌

---

## 🔒 Sécurité

### ⚠️ Ne pas commiter config.php sur GitHub

1. Le fichier est déjà dans `.gitignore`
2. Vérifier que git l'ignore :
   ```
   git status
   ```
3. config.php ne doit PAS apparaître en rouge

### Si config.php a déjà été commité :
```bash
git rm --cached php/config.php
git commit -m "Stop tracking config.php"
```

---

## 🌐 En production (sur un serveur réel)

### Créer le même config.php sur le serveur

1. Via SFTP/FTP, créer `php/config.php` sur le serveur
2. Configurer avec :
   - Email Gmail
   - App Password générée
   - Identifiants MySQL du serveur (peut être différent)

3. Les emails fonctionneront automatiquement

---

## 📞 Questions fréquentes

**Q : Comment changer l'email destinataire ?**  
R : Modifier `ADMIN_EMAIL` dans `php/config.php`

**Q : Puis-je utiliser Outlook/Yahoo au lieu de Gmail ?**  
R : Oui, mais les serveurs SMTP sont différents. Contactez le support de votre provider email.

**Q : Les emails vont-ils dans le spam ?**  
R : Possibilité. Vérifier le dossier "Promotions" ou "Spam" de Gmail

**Q : Puis-je tester sans App Password ?**  
R : Non, c'est obligatoire pour la sécurité Google

**Q : Combien d'emails je peux envoyer par jour ?**  
R : Gmail autorise ~500 emails/jour en développement

---

## ✅ Checklist finale

- [ ] Vérification 2FA activée sur votre compte Google
- [ ] App Password généré depuis myaccount.google.com
- [ ] `SMTP_PASS` rempli dans `php/config.php`
- [ ] Formulaire met à jour pour utiliser `contact-v2.php`
- [ ] Test réussi via `gmail-setup.php`
- [ ] Email de test reçu dans Gmail
- [ ] Formulaire principal fonctionne
- [ ] Messages apparaissent en phpMyAdmin

---

*Configuration complétée ! 🎉*
