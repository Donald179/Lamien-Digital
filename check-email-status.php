<?php
/**
 * Vérifier le statut des emails envoyés
 * Diagnostic complet du système d'email
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Email — TechBurk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: monospace;
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            line-height: 1.6;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { margin-bottom: 20px; color: #00ff00; }
        h2 { margin-top: 30px; margin-bottom: 10px; color: #ffaa00; }
        .section {
            background: #2d2d2d;
            border: 1px solid #444;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .success { color: #00ff00; }
        .error { color: #ff4444; }
        .warning { color: #ffaa00; }
        .info { color: #4488ff; }
        pre {
            background: #1a1a1a;
            border: 1px solid #333;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }
        th { background: #333; }
        .empty { color: #888; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 DIAGNOSTIC EMAIL TECHBURK</h1>

        <?php
        require_once 'php/config.php';

        // ─── 1. VÉRIFIER LA CONFIGURATION ───
        echo '<div class="section">';
        echo '<h2>1️⃣ Configuration SMTP</h2>';
        
        $config_ok = true;
        
        if (defined('SMTP_USER') && SMTP_USER) {
            echo '<span class="success">✓ SMTP_USER:</span> ' . SMTP_USER . '<br>';
        } else {
            echo '<span class="error">✗ SMTP_USER: NON DÉFINI</span><br>';
            $config_ok = false;
        }
        
        if (defined('SMTP_PASS') && SMTP_PASS) {
            echo '<span class="success">✓ SMTP_PASS:</span> ••••••••••••••••<br>';
        } else {
            echo '<span class="error">✗ SMTP_PASS: NON DÉFINI OU VIDE</span><br>';
            $config_ok = false;
        }
        
        if (defined('SMTP_HOST') && SMTP_HOST) {
            echo '<span class="success">✓ SMTP_HOST:</span> ' . SMTP_HOST . '<br>';
        } else {
            echo '<span class="error">✗ SMTP_HOST: NON DÉFINI</span><br>';
            $config_ok = false;
        }
        
        if (defined('SMTP_PORT') && SMTP_PORT) {
            echo '<span class="success">✓ SMTP_PORT:</span> ' . SMTP_PORT . '<br>';
        } else {
            echo '<span class="error">✗ SMTP_PORT: NON DÉFINI</span><br>';
            $config_ok = false;
        }
        
        if (defined('ADMIN_EMAIL') && ADMIN_EMAIL) {
            echo '<span class="success">✓ ADMIN_EMAIL:</span> ' . ADMIN_EMAIL . '<br>';
        } else {
            echo '<span class="error">✗ ADMIN_EMAIL: NON DÉFINI</span><br>';
            $config_ok = false;
        }
        
        echo '</div>';

        // ─── 2. VÉRIFIER LA BASE DE DONNÉES ───
        echo '<div class="section">';
        echo '<h2>2️⃣ Base de Données</h2>';
        
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            echo '<span class="success">✓ Connexion MySQL OK</span><br>';
            
            // Vérifier la table
            $result = $pdo->query("SHOW TABLES LIKE 'messages_contact'");
            if ($result->rowCount() > 0) {
                echo '<span class="success">✓ Table messages_contact existe</span><br>';
                
                // Compter les messages
                $count = $pdo->query("SELECT COUNT(*) as total FROM messages_contact")->fetchColumn();
                echo "<span class=\"info\">📊 Nombre de messages:</span> <strong>$count</strong><br>";
                
                // Afficher les derniers messages
                if ($count > 0) {
                    echo '<h3 style="margin-top: 15px;">Derniers messages:</h3>';
                    $messages = $pdo->query("
                        SELECT id, nom, telephone, email, service, message, date_created, email_sent 
                        FROM messages_contact 
                        ORDER BY date_created DESC 
                        LIMIT 5
                    ")->fetchAll();
                    
                    echo '<table>';
                    echo '<tr><th>ID</th><th>Nom</th><th>Tél</th><th>Email</th><th>Service</th><th>Date</th><th>Envoyé?</th></tr>';
                    foreach ($messages as $msg) {
                        $sent_status = $msg['email_sent'] ? '<span class="success">✓ OUI</span>' : '<span class="error">✗ NON</span>';
                        echo '<tr>';
                        echo '<td>' . $msg['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($msg['nom']) . '</td>';
                        echo '<td>' . htmlspecialchars($msg['telephone']) . '</td>';
                        echo '<td>' . htmlspecialchars($msg['email'] ?? 'N/A') . '</td>';
                        echo '<td>' . htmlspecialchars($msg['service']) . '</td>';
                        echo '<td>' . htmlspecialchars($msg['date_created']) . '</td>';
                        echo '<td>' . $sent_status . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<span class="warning">⚠️ Aucun message dans la base de données</span><br>';
                }
            } else {
                echo '<span class="error">✗ Table messages_contact N\'EXISTE PAS</span><br>';
            }
        } catch (Exception $e) {
            echo '<span class="error">✗ Erreur MySQL:</span> ' . $e->getMessage() . '<br>';
        }
        
        echo '</div>';

        // ─── 3. VÉRIFIER LES LOGS ───
        echo '<div class="section">';
        echo '<h2>3️⃣ Fichiers de Logs</h2>';
        
        $log_file = 'logs/emails.log';
        if (file_exists($log_file)) {
            $content = file_get_contents($log_file);
            echo '<span class="success">✓ Fichier logs/emails.log existe</span><br>';
            echo '<span class="info">Taille:</span> ' . number_format(filesize($log_file)) . ' octets<br>';
            echo '<pre>' . htmlspecialchars(tail($log_file, 20)) . '</pre>';
        } else {
            echo '<span class="warning">⚠️ Fichier logs/emails.log n\'existe pas encore</span><br>';
            echo '<span class="info">ℹ️ Il sera créé après le premier envoi</span><br>';
        }
        
        echo '</div>';

        // ─── 4. VÉRIFIER LES PERMISSIONS ───
        echo '<div class="section">';
        echo '<h2>4️⃣ Permissions Fichiers</h2>';
        
        $dirs_to_check = [
            'logs' => 'Dossier des logs',
            'php' => 'Dossier PHP',
            '.' => 'Racine du site',
        ];
        
        foreach ($dirs_to_check as $dir => $label) {
            if (is_dir($dir)) {
                $perms = substr(sprintf('%o', fileperms($dir)), -4);
                $writable = is_writable($dir) ? '<span class="success">✓ Writable</span>' : '<span class="error">✗ Read-only</span>';
                echo "<span class=\"info\">$label ($dir):</span> chmod $perms - $writable<br>";
            }
        }
        
        echo '</div>';

        // ─── 5. VÉRIFIER FSOCKOPEN ───
        echo '<div class="section">';
        echo '<h2>5️⃣ Fonction FSOCKOPEN</h2>';
        
        if (function_exists('fsockopen')) {
            echo '<span class="success">✓ fsockopen disponible</span><br>';
            
            // Tester la connexion à SMTP
            echo '<span class="info">Tentative de connexion à ' . SMTP_HOST . ':' . SMTP_PORT . '...</span><br>';
            
            $socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 5);
            if ($socket) {
                echo '<span class="success">✓ Connexion SMTP OK</span><br>';
                $response = fgets($socket);
                echo '<pre>' . htmlspecialchars($response) . '</pre>';
                fclose($socket);
            } else {
                echo '<span class="error">✗ Impossible de se connecter à SMTP</span><br>';
                echo '<span class="error">Erreur:</span> ' . $errstr . " (Code: $errno)<br>";
            }
        } else {
            echo '<span class="error">✗ fsockopen NON disponible (extension disabled)</span><br>';
        }
        
        echo '</div>';

        // ─── 6. TEST D'ENVOI ───
        echo '<div class="section">';
        echo '<h2>6️⃣ Test d\'Envoi</h2>';
        
        if (isset($_GET['test_send'])) {
            echo '<span class="info">Envoi d\'un email de test...</span><br>';
            
            require_once 'php/mailer-smtp.php';
            
            $mailer = new TechBurkMailerSMTP(SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, ADMIN_EMAIL);
            
            $result = $mailer->sendContactNotification(
                'Test User',
                '+226 55 75 72 99',
                'test@example.com',
                'maintenance',
                'Ceci est un message de test de diagnostic.',
                'TEST-' . time()
            );
            
            if ($result) {
                echo '<span class="success">✓ Email envoyé avec succès!</span><br>';
                echo '<span class="info">Vérifiez votre boîte mail ' . ADMIN_EMAIL . '</span><br>';
            } else {
                echo '<span class="error">✗ Erreur lors de l\'envoi</span><br>';
                echo '<span class="info">Vérifiez les logs pour plus de détails</span><br>';
            }
        } else {
            echo '<form method="GET" style="margin-top: 10px;">';
            echo '<button type="submit" name="test_send" value="1" style="background: #00aa00; color: #000; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px;">';
            echo '▶️ Envoyer un email de test';
            echo '</button>';
            echo '</form>';
        }
        
        echo '</div>';
        ?>
    </div>

    <?php
    /**
     * Lire les N dernières lignes d'un fichier
     */
    function tail($file, $lines = 20) {
        if (!file_exists($file)) return '';
        
        $file_handle = fopen($file, 'r');
        if (!$file_handle) return 'Impossible de lire le fichier';
        
        $line_count = 0;
        $file_contents = '';
        $size = -1;
        
        while (fseek($file_handle, $size, SEEK_END) === 0) {
            $char = fgetc($file_handle);
            if ($char === "\n") {
                $line_count++;
                if ($line_count == $lines) break;
            }
            $file_contents = $char . $file_contents;
            $size--;
        }
        
        fclose($file_handle);
        return ltrim($file_contents, "\n");
    }
    ?>
</body>
</html>
