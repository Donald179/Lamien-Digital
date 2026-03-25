# 🚀 TechBurk — Expert Informatique Burkina Faso
## Guide d'installation complet

---

## 📦 Structure du projet

```
techburk/
├── index.html              ← Page principale (toutes sections)
├── css/
│   └── style.css           ← Styles complets (responsive)
├── js/
│   └── script.js           ← Animations & interactions JS
├── php/
│   ├── contact.php         ← Backend formulaire de contact
│   └── mailer.php          ← Système d'envoi d'emails
├── database.sql            ← Script SQL (base de données)
├── EMAIL_CONFIG.md         ← Guide configuration emails
└── README.md               ← Ce fichier
```

---

## 🖥️ Installation en local avec XAMPP

### Étape 1 — Installer XAMPP
1. Télécharger XAMPP sur [apachefriends.org](https://www.apachefriends.org/fr/index.html)
2. Installer avec les options par défaut
3. Lancer **XAMPP Control Panel**
4. Démarrer **Apache** et **MySQL**

---

### Étape 2 — Copier les fichiers du site
1. Ouvrir le dossier XAMPP (généralement `C:\xampp\`)
2. Aller dans le dossier `htdocs`
3. Créer un dossier `techburk`
4. Copier **tous les fichiers** du projet dans `C:\xampp\htdocs\techburk\`

```
C:\xampp\htdocs\techburk\
├── index.html
├── css\style.css
├── js\script.js
├── php\contact.php
├── php\mailer.php
└── database.sql
```

---

### Étape 3 — Créer la base de données
1. Ouvrir votre navigateur et aller sur : `http://localhost/phpmyadmin`
2. Cliquer sur **"Importer"** dans le menu du haut
3. Cliquer sur **"Choisir un fichier"**
4. Sélectionner le fichier `database.sql`
5. Cliquer sur **"Exécuter"**

✅ La base de données `techburk_db` sera créée automatiquement avec :
- Toutes les tables
- Les données d'exemple

---

### Étape 4 — Configurer la connexion BDD (si nécessaire)
Ouvrir le fichier `php/contact.php` et vérifier les paramètres :

```php
define('DB_HOST', 'localhost');   // Laisser localhost
define('DB_NAME', 'techburk_db'); // Nom de la BDD
define('DB_USER', 'root');         // Utilisateur XAMPP (root par défaut)
define('DB_PASS', '');             // Mot de passe (vide par défaut sur XAMPP)
define('ADMIN_EMAIL', 'lamiendonaldo179@gmail.com'); // Votre email
```

---

### Étape 5 — Tester le site
1. Ouvrir le navigateur
2. Aller sur : **`http://localhost/techburk/`**
3. Le site doit s'afficher correctement ✅
4. **Tester l'email** : accéder à `http://localhost/techburk/php/test_email.php`

---

## 📧 Système d'emails

Le formulaire de contact envoie automatiquement les messages à : **lamiendonaldo179@gmail.com**

✅ Fonctionnalités :
- Validation des données (nom, téléphone, email, message)
- Enregistrement en base de données
- Envoi d'email automatique à l'admin
- Logging de tous les emails
- Classe Mailer réutilisable

📝 Pour plus de détails, voir : [EMAIL_CONFIG.md](EMAIL_CONFIG.md)

---

## 📱 Personnalisation obligatoire

### 1. Numéro WhatsApp
Remplacer `22655757299` par votre vrai numéro dans :
- `index.html` (tous les liens `wa.me/226XXXXXXXX`)

### 2. Email
Remplacer `lamiendonaldo179@gmail.com` par votre email réel dans :
- `index.html` (section contact)
- `php/contact.php` → `define('ADMIN_EMAIL', 'votre@email.com');`

### 3. Nom de l'entreprise
Remplacer `TechBurk` / `LAMIEN DIGITAL` par votre vrai nom dans :
- `index.html` (balise `<title>`, logo, footer)
- `php/contact.php`

---

## 🌐 Mise en ligne (hébergement)

### Option 1 — Hébergeur classique (cPanel)
1. Accéder à votre **cPanel**
2. Aller dans **Gestionnaire de fichiers** → `public_html`
3. Uploader tous les fichiers (sauf `database.sql`)
4. Aller dans **phpMyAdmin** → Créer BDD → Importer `database.sql`
5. Modifier `php/contact.php` avec les nouveaux identifiants BDD

### Option 2 — Hébergement partagé (InfinityFree, 000webhost...)
1. Créer un compte sur [InfinityFree](https://infinityfree.net/) (gratuit)
2. Créer un site et noter les infos BDD
3. Uploader via le gestionnaire de fichiers
4. Importer `database.sql` via phpMyAdmin
5. Adapter les identifiants dans `contact.php`

---

## 🔒 Sécurité (avant mise en ligne)

- [ ] Modifier l'email admin dans `php/contact.php`
- [ ] Activer HTTPS sur votre hébergement
- [ ] Modifier le mot de passe MySQL (pas root/vide en production)
- [ ] Supprimer `php/test_email.php` en production
- [ ] Vérifier les permissions de fichiers

---

## 🛠️ Technologies utilisées

| Technologie | Usage |
|-------------|-------|
| HTML5       | Structure des pages |
| CSS3        | Design responsive |
| JavaScript  | Animations & interactions |
| PHP 7.4+    | Backend formulaire |
| MySQL 5.7+  | Base de données |
| Font Awesome 6 | Icônes |
| Google Fonts | Typographies |

---

## 🧪 Voir les messages reçus

Dans phpMyAdmin, exécutez :
```sql
USE techburk_db;
SELECT * FROM messages_contact ORDER BY date_envoi DESC;
```

---

## ❓ Problèmes courants

**Le formulaire ne fonctionne pas ?**
→ Vérifiez que Apache ET MySQL sont démarrés dans XAMPP

**Erreur de connexion BDD ?**
→ Vérifiez les identifiants dans `php/contact.php`

**Les emails ne s'envoient pas ?**
→ Consultez `php/test_email.php` et [EMAIL_CONFIG.md](EMAIL_CONFIG.md)

---

## 📞 Support

Pour toute question sur ce projet, consultez :
- [EMAIL_CONFIG.md](EMAIL_CONFIG.md) - Configuration emails
- Documentation XAMPP officielle
- Documentation PHP officielle

---

*🚀 TechBurk — Expert Informatique Burkina Faso*  
*Fait avec ❤️ pour les entrepreneurs Burkinabè*
