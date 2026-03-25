# Configuration Email - TechBurk

## 📧 Système d'envoi d'email activé

L'envoi de messages via le formulaire de contact est maintenant **complètement activé** et configuré pour envoyer les messages à : **lamiendonaldo179@gmail.com**

## 🔧 Comment ça fonctionne ?

### 1. **Flux du formulaire**
- L'utilisateur remplit le formulaire de contact (nom, téléphone, email, service, message)
- Clique sur "Envoyer le message"
- Le formulaire envoie les données en AJAX
- Le serveur PHP valide les données
- Les messages sont enregistrés en base de données
- **Un email est automatiquement envoyé à votre adresse email**

### 2. **Traitement backend**
Les fichiers impliqués :
- `php/contact.php` - Valide et traite les données
- `php/mailer.php` - Classe pour l'envoi d'emails (nouvelle)
- `js/script.js` - Gère le formulaire en AJAX
- `index.html` - Contient le formulaire

### 3. **Email reçu**
Vous recevrez un email structuré avec :
```
Nom          : [Nom du client]
Téléphone    : [Numéro de téléphone]
Email        : [Email du client, ou "Non renseigné"]
Service      : [Service sélectionné]
Date         : [Date et heure]

MESSAGE :
[Le contenu du message]

ID message   : [Numéro unique]
IP client    : [Adresse IP du client]
```

## ✅ Pré-requis

### Configuration serveur nécessaire
Votre serveur PHP doit avoir :
1. **La fonction `mail()` activée** (généralement activée par défaut)
2. **Un serveur SMTP configuré** (fourni par votre hébergeur)

### Vérifier la configuration
Sur XAMPP (Windows) :
- Ouvrir `php.ini`
- Chercher la section `[mail function]`
- S'assurer que : `mail.add_x_header = On`

Sur un hébergeur :
- Vérifier auprès du support que la fonction mail() est disponible
- Vérifier que les SMTP configurations sont correctes

## 📝 Logs des emails

Un fichier de log est automatiquement créé pour tracer les emails :
- **Localisation** : `logs/emails.log`
- **Contenu** : Timestamp, statut (SENT/FAILED), ID du message, informations du client
- **Usage** : Vérifier si les emails ont bien été envoyés

## 🧪 Tester le système

### Sur XAMPP (développement local)
1. Ouvrir `http://localhost/techburk` (ou votre URL locale)
2. Scroller jusqu'à la section "Contact"
3. Remplir le formulaire complètement
4. Cliquer sur "Envoyer le message"
5. Vous devriez voir un message "Message envoyé !"

⚠️ **Note** : Sur XAMPP local sans serveur SMTP, les emails peuvent ne pas être envoyés. Mais ils seront enregistrés en base de données.

### Sur un hébergeur en ligne
- Le même formulaire fonctionne automatiquement
- Les emails sont envoyés à lamiendonaldo179@gmail.com
- Consultez les logs pour confirmer

## 🔍 Déboguer si ça ne fonctionne pas

### Vérifier les logs
1. Accéder au dossier `php/logs/emails.log`
2. Voir le statut : `SENT` ou `FAILED`

### Cas 1 : "FAILED" dans les logs
**Cause possible** : Serveur mail() non configuré
**Solutions** :
- Vérifier la configuration PHP auprès de l'hébergeur
- Demander l'activation de la fonction mail()
- Utiliser une alternative comme Amazon SES, SendGrid, ou Mailgun (voir section avancée)

### Cas 2 : Pas de fichier email.log
**Cause possible** : Dossier `php/logs/` n'existe pas ou pas de permissions
**Solutions** :
- Le dossier `logs/` sera créé automatiquement à la première utilisation
- Vérifier les permissions du dossier `php/`

### Cas 3 : Base de données vide
**Cause possible** : Connexion MySQL non configurée
**Solutions** :
- Vérifier la configuration dans `php/contact.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS)
- S'assurer que la base de données existe
- S'assurer que la table `messages_contact` existe

## 💡 Configuration avancée (Alternative SMTP)

Si le serveur n'a pas mail() activé, vous pouvez utiliser une classe mailer comme PHPMailer.
Installez PHPMailer via Composer :

```bash
composer require phpmailer/phpmailer
```

Ensuite, modifiez `php/mailer.php` pour utiliser PHPMailer avec Gmail :

```php
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'votre_email@gmail.com';
$mail->Password = 'votre_mot_de_passe_app';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->setFrom('noreply@techburk.bf');
$mail->addAddress(ADMIN_EMAIL);
$mail->isHTML(false);
$mail->Subject = $subject;
$mail->Body = $body;
$mail->send();
```

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifier les logs dans `php/logs/emails.log`
2. Consulter la documentation de votre hébergeur
3. Vérifier la configuration MySQL

---

**Mis à jour** : 25 Décembre 2025  
**Email configuré** : lamiendonaldo179@gmail.com
