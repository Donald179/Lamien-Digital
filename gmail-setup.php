<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Gmail SMTP — TechBurk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); padding: 30px; margin-bottom: 20px; }
        h1 { color: #333; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 14px; }
        .step { margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .step:last-child { border-bottom: none; }
        h2 { font-size: 16px; color: #667eea; margin-bottom: 15px; font-weight: bold; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .code-block { background: #f4f4f4; border-left: 4px solid #667eea; padding: 12px; margin: 10px 0; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 13px; overflow-x: auto; }
        button { background: #667eea; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        button:hover { background: #5568d3; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; }
        .config-item { background: #f9f9f9; padding: 10px; border-radius: 4px; margin-bottom: 10px; font-family: 'Courier New', monospace; font-size: 12px; }
        .config-label { font-weight: bold; color: #667eea; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>📧 Configuration Gmail SMTP</h1>
            <p class="subtitle">Pour recevoir les messages de contact par email</p>

            <!-- Étape 1 -->
            <div class="step">
                <h2>📍 Étape 1 : Générer un App Password Google</h2>
                
                <div class="alert warning">
                    <strong>⚠️ Important:</strong> Ceci ne fonctionne que si vous avez activé la "Vérification en 2 étapes" sur votre compte Google
                </div>

                <p style="margin-bottom: 15px; font-size: 14px;"><strong>Suivez ces étapes :</strong></p>
                
                <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                    <ol style="margin-left: 20px; line-height: 1.8;">
                        <li>Aller à <code>https://myaccount.google.com</code></li>
                        <li>Cliquer sur <strong>Sécurité</strong> (menu de gauche)</li>
                        <li>Chercher <strong>"Mots de passe des applications"</strong></li>
                        <li>Sélectionner:
                            <ul style="margin-top: 10px;">
                                <li>App: <strong>Mail</strong></li>
                                <li>Device: <strong>Windows (ou votre OS)</strong></li>
                            </ul>
                        </li>
                        <li>Cliquer <strong>Générer</strong></li>
                        <li>Copier le mot de passe généré (16 caractères)</li>
                    </ol>
                </div>

                <div class="alert info">
                    💡 <strong>Note:</strong> Google génère un mot de passe spécial. Ce n'est pas votre mot de passe habituel.
                </div>
            </div>

            <!-- Étape 2 -->
            <div class="step">
                <h2>⚙️ Étape 2 : Configurer config.php</h2>
                
                <p style="margin-bottom: 15px; font-size: 14px;">Ouvrez le fichier <code>php/config.php</code> et remplissez :</p>
                
                <div class="code-block">
# Votre email Gmail
SMTP_USER = lamiendonaldo179@gmail.com

# Le mot de passe généré (16 caractères sans espaces)
SMTP_PASS = xyzabcdefghijklm
                </div>

                <div class="alert warning">
                    ⚠️ <strong>Sécurité:</strong> Ne commitez jamais ce fichier sur GitHub!
                </div>
            </div>

            <!-- Étape 3 -->
            <div class="step">
                <h2>🧪 Étape 3 : Tester l'envoi</h2>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Email destinataire (où recevoir les messages):</label>
                        <input type="email" name="test_email" value="lamiendonaldo179@gmail.com" required>
                    </div>
                    
                    <button type="submit" name="test_send">
                        <i class="fas fa-envelope"></i> Envoyer un email de test
                    </button>
                </form>

                <div id="testResult" style="margin-top: 15px;"></div>
            </div>

            <!-- Étape 4 -->
            <div class="step">
                <h2>✅ Étape 4 : Configuration complète</h2>
                
                <p style="margin-bottom: 15px; font-size: 14px;">Une fois testé avec succès :</p>
                
                <ol style="margin-left: 20px; line-height: 1.8;">
                    <li>Les messages seront enregistrés en base de données</li>
                    <li>Les emails seront envoyés à <code>lamiendonaldo179@gmail.com</code></li>
                    <li>Consultez phpMyAdmin pour voir les messages</li>
                </ol>

                <div class="config-item" style="margin-top: 15px;">
                    <span class="config-label">📍 Base de données:</span><br>
                    http://localhost/phpmyadmin
                </div>

                <div class="config-item">
                    <span class="config-label">📊 Voir les messages:</span><br>
                    SELECT * FROM techburk_db.messages_contact;
                </div>
            </div>

            <!-- Fichiers importants -->
            <div class="step">
                <h2>📁 Fichiers à connaître</h2>
                
                <div class="config-item">
                    <span class="config-label">php/config.php</span><br>
                    Configuration Gmail SMTP (à personnaliser)
                </div>

                <div class="config-item">
                    <span class="config-label">php/contact-v2.php</span><br>
                    Formulaire de contact avec Gmail SMTP
                </div>

                <div class="config-item">
                    <span class="config-label">php/mailer-smtp.php</span><br>
                    Classe pour envoyer les emails via Gmail
                </div>

                <div class="config-item">
                    <span class="config-label">index.html</span><br>
                    À modifier pour utiliser contact-v2.php (voir ci-dessous)
                </div>
            </div>

            <!-- Mettre à jour index.html -->
            <div class="step">
                <h2>🔄 Étape 5 : Mettre à jour le formulaire</h2>
                
                <p style="margin-bottom: 15px; font-size: 14px;"><strong>Modifiez la ligne du formulaire dans index.html :</strong></p>
                
                <p style="color: #999; margin-bottom: 10px;">Cherchez :</p>
                <div class="code-block">
&lt;form id="contactForm" action="php/contact.php" method="POST"&gt;
                </div>

                <p style="color: #999; margin-bottom: 10px;">Remplacez par :</p>
                <div class="code-block">
&lt;form id="contactForm" action="php/contact-v2.php" method="POST"&gt;
                </div>

                <div class="alert info">
                    Après cela, le formulaire utilisera Gmail SMTP pour envoyer les emails.
                </div>
            </div>
        </div>

        <!-- Test Result -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_send'])) {
            $test_email = $_POST['test_email'] ?? '';
            
            if (empty($test_email)) {
                echo '<div class="card"><div class="alert error">Veuillez entrer un email</div></div>';
            } else {
                require_once 'config.php';
                require_once 'mailer-smtp.php';

                try {
                    $mailer = new TechBurkMailerSMTP(
                        $test_email,
                        SITE_NAME,
                        SMTP_USER,
                        SMTP_PASS
                    );

                    // Envoyer un email de test
                    $body = "Ceci est un email de test de TechBurk SMTP\n";
                    $body .= "Date: " . date('Y-m-d H:i:s') . "\n";
                    $body .= "Configuration OK!";

                    $sent = $mailer->sendContactNotification(
                        'Test User',
                        '+226 55 75 72 99',
                        $test_email,
                        'autre',
                        'Email de test',
                        999999,
                        $_SERVER['REMOTE_ADDR']
                    );

                    echo '<div class="card">';
                    if ($sent) {
                        echo '<div class="alert success">
                            <i class="fas fa-check-circle"></i> <strong>Email envoyé avec succès!</strong><br>
                            Vérifiez votre boîte mail: ' . htmlspecialchars($test_email) . '
                        </div>';
                    } else {
                        echo '<div class="alert error">
                            <i class="fas fa-times-circle"></i> <strong>Erreur lors de l\'envoi</strong><br>
                            Vérifiez:<br>
                            - SMTP_PASS est rempli dans config.php<br>
                            - Le mot de passe est correct<br>
                            - La vérification 2FA est activée sur Google
                        </div>';
                    }
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div class="card"><div class="alert error">
                        <strong>Erreur:</strong> ' . htmlspecialchars($e->getMessage()) . '
                    </div></div>';
                }
            }
        }
        ?>
    </div>
</body>
</html>
