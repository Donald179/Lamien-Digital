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
│   └── contact.php         ← Backend formulaire de contact
├── database.sql            ← Script SQL (base de données)
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
- Les procédures stockées

---

### Étape 4 — Configurer la connexion BDD (si nécessaire)
Ouvrir le fichier `php/contact.php` et vérifier les paramètres :

```php
define('DB_HOST', 'localhost');   // Laisser localhost
define('DB_NAME', 'techburk_db'); // Nom de la BDD
define('DB_USER', 'root');         // Utilisateur XAMPP (root par défaut)
define('DB_PASS', '');             // Mot de passe (vide par défaut sur XAMPP)
```

---

### Étape 5 — Tester le site
1. Ouvrir le navigateur
2. Aller sur : **`http://localhost/techburk/`**
3. Le site doit s'afficher correctement ✅

---

## 📱 Personnalisation obligatoire

### 1. Numéro WhatsApp
Remplacer `22600000000` par votre vrai numéro dans :
- `index.html` (tous les liens `wa.me/226XXXXXXXX`)
- Chercher : `22600000000` et remplacer par votre numéro

### 2. Email
Remplacer `contact@techburk.bf` par votre email réel dans :
- `index.html`
- `php/contact.php` → `define('ADMIN_EMAIL', 'votre@email.com');`

### 3. Nom de l'entreprise
Remplacer `TechBurk` par votre vrai nom dans :
- `index.html` (balise `<title>`, logo, footer)
- `php/contact.php`

### 4. Téléphone
Remplacer `+226 00 00 00 00` par votre vrai numéro dans :
- `index.html` (section contact & footer)

### 5. Localisation
Modifier votre adresse exacte dans la section contact.

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

- [ ] Changer le mot de passe admin dans la table `admin_users`
- [ ] Activer HTTPS sur votre hébergement
- [ ] Modifier le mot de passe MySQL (pas root/vide en production)
- [ ] Vérifier les permissions de fichiers (644 pour fichiers, 755 pour dossiers)

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
| Google Fonts (Syne + Outfit) | Typographies |

---

## 📊 Voir les messages reçus

Dans phpMyAdmin, exécutez :
```sql
USE techburk_db;
SELECT * FROM messages_contact ORDER BY date_envoi DESC;
```

Pour voir les messages non lus :
```sql
SELECT * FROM v_messages_non_lus;
```

---

## ❓ Problèmes courants

**Le formulaire ne fonctionne pas ?**
→ Vérifiez que Apache ET MySQL sont démarrés dans XAMPP
→ Vérifiez l'URL dans le formulaire (doit pointer vers `php/contact.php`)

**Page blanche après envoi ?**
→ Activez l'affichage des erreurs PHP : `ini_set('display_errors', 1);` en début de `contact.php`

**Erreur de connexion BDD ?**
→ Vérifiez les identifiants dans `php/contact.php`
→ Assurez-vous que la base `techburk_db` existe dans phpMyAdmin

---

## 📞 Support

Pour toute question sur ce projet, consultez la documentation XAMPP ou PHP officielle.

---

*TechBurk — Fait avec ❤️ pour les entrepreneurs Burkinabè*
