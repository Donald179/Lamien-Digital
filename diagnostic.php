<?php
/**
 * TechBurk — Diagnostic Contact
 * diagnostic.php
 * 
 * Script pour diagnostiquer les problèmes du formulaire de contact
 */

header('Content-Type: text/html; charset=utf-8');

// Afficher tous les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechBurk — Diagnostic Contact</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h1 { color: #333; margin-bottom: 30px; }
        .section { margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .section:last-child { border-bottom: none; }
        h2 { font-size: 16px; color: #1a7fff; margin-bottom: 10px; font-weight: bold; }
        .check { padding: 10px; margin: 8px 0; border-radius: 4px; font-size: 14px; font-family: 'Courier New', monospace; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; font-size: 13px; }
        th { background: #f9f9f9; font-weight: bold; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        .test-btn { background: #1a7fff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; margin-top: 15px; }
        .test-btn:hover { background: #1568e6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic Contact TechBurk</h1>

        <!-- 1. PHP CONFIGURATION -->
        <div class="section">
            <h2>📋 Configuration PHP</h2>
            
            <?php
            $checks = [
                'PHP Version' => phpversion(),
                'Email support' => function_exists('mail') ? '✓ OUI' : '✗ NON',
                'Extension MySQLi' => extension_loaded('mysqli') ? '✓ OUI' : '✗ NON',
                'Extension PDO' => extension_loaded('pdo') ? '✓ OUI' : '✗ NON',
                'Max upload size' => ini_get('upload_max_filesize'),
                'Post max size' => ini_get('post_max_size'),
            ];

            foreach ($checks as $name => $value) {
                echo "<div class='check info'><strong>$name:</strong> $value</div>";
            }
            ?>
        </div>

        <!-- 2. DATABASE CONNECTION -->
        <div class="section">
            <h2>🗄️ Connexion Base de Données</h2>
            
            <?php
            $db_config = [
                'host'     => 'localhost',
                'database' => 'techburk_db',
                'user'     => 'root',
                'password' => '',
            ];

            try {
                $pdo = new PDO(
                    'mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['database'] . ';charset=utf8mb4',
                    $db_config['user'],
                    $db_config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );

                echo "<div class='check success'>✓ Connexion à la BD réussie!</div>";

                // Vérifier les tables
                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                echo "<div class='check info'><strong>Tables trouvées:</strong><br>";
                
                if (empty($tables)) {
                    echo "<span class='error'>Aucune table trouvée!</span>";
                } else {
                    foreach ($tables as $table) {
                        echo "- $table<br>";
                    }
                }
                echo "</div>";

                // Vérifier la table messages_contact
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM messages_contact");
                $result = $stmt->fetch();
                echo "<div class='check info'><strong>Messages reçus:</strong> " . $result['count'] . "</div>";

            } catch (PDOException $e) {
                echo "<div class='check error'><strong>✗ Erreur de connexion:</strong><br>";
                echo htmlspecialchars($e->getMessage());
                echo "</div>";

                echo "<div class='check warning'><strong>⚠ Solution:</strong><br>";
                echo "1. Assurez-vous que XAMPP est lancé (Apache + MySQL)<br>";
                echo "2. Vérifiez les identifiants dans contact.php<br>";
                echo "3. Créez la base 'techburk_db' via phpMyAdmin";
                echo "</div>";
            }
            ?>
        </div>

        <!-- 3. FILE PERMISSIONS -->
        <div class="section">
            <h2>📁 Permissions Fichiers</h2>
            
            <?php
            $files = [
                'php/contact.php' => __DIR__ . '/php/contact.php',
                'php/mailer.php' => __DIR__ . '/php/mailer.php',
                'logs/' => __DIR__ . '/logs',
            ];

            foreach ($files as $name => $path) {
                $exists = file_exists($path);
                $writable = is_writable($path);
                
                if ($exists && is_readable($path)) {
                    echo "<div class='check success'>✓ $name - Accessible</div>";
                } elseif (!$exists) {
                    echo "<div class='check error'>✗ $name - N'existe pas!</div>";
                } else {
                    echo "<div class='check warning'>⚠ $name - Existe mais pas accessible</div>";
                }
            }
            ?>
        </div>

        <!-- 4. TEST FORM SUBMISSION -->
        <div class="section">
            <h2>📧 Test d'Envoi</h2>
            
            <form method="POST" action="php/contact.php">
                <div style="margin-bottom: 10px;">
                    <label>Nom: <input type="text" name="nom" value="Test" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></label>
                </div>
                <div style="margin-bottom: 10px;">
                    <label>Téléphone: <input type="tel" name="telephone" value="+226 55 75 72 99" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></label>
                </div>
                <div style="margin-bottom: 10px;">
                    <label>Email: <input type="email" name="email" value="test@test.com" style="width: 100%; padding: 5px; border: 1px solid #ddd;"></label>
                </div>
                <div style="margin-bottom: 10px;">
                    <label>Service: <select name="service" style="width: 100%; padding: 5px; border: 1px solid #ddd;">
                        <option value="diagnostic">Diagnostic informatique</option>
                    </select></label>
                </div>
                <div style="margin-bottom: 10px;">
                    <label>Message: <textarea name="message" style="width: 100%; padding: 5px; border: 1px solid #ddd; height: 80px;">Ceci est un message de test du formulaire de diagnostic.</textarea></label>
                </div>
                <button type="submit" class="test-btn">🚀 Tester l'envoi</button>
            </form>

            <div class="check info" style="margin-top: 15px;">
                Après avoir cliqué sur "Tester l'envoi", vous verrez la réponse du serveur ici.
            </div>
        </div>

        <!-- 5. LOGS -->
        <div class="section">
            <h2>📝 Derniers Logs</h2>
            
            <?php
            $log_file = __DIR__ . '/logs/emails.log';
            if (file_exists($log_file)) {
                $logs = file_get_contents($log_file);
                echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto; font-size: 12px;'>";
                echo htmlspecialchars($logs);
                echo "</pre>";
            } else {
                echo "<div class='check warning'>Aucun fichier de log trouvé. Il sera créé après le premier envoi.</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
