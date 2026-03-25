<?php
/**
 * Test simple de submission du formulaire
 */

header('Content-Type: application/json; charset=utf-8');

// Enregistrer tout ce qui arrive
$log_file = 'logs/form-test.log';
@mkdir('logs', 0777, true);

// Log la requête
$log = "=== REQUEST " . date('Y-m-d H:i:s') . " ===\n";
$log .= "METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
$log .= "POST DATA: " . json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
$log .= "FILES: " . json_encode($_FILES, JSON_PRETTY_PRINT) . "\n";
$log .= "PHP ERRORS: " . (defined('DISPLAY_ERRORS') ? 'ON' : 'OFF') . "\n";
file_put_contents($log_file, $log, FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données
    $nom = $_POST['nom'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $email = $_POST['email'] ?? '';
    $service = $_POST['service'] ?? '';
    $message = $_POST['message'] ?? '';

    // Validation basique
    $errors = [];
    if (empty($nom)) $errors[] = 'Nom requis';
    if (empty($telephone)) $errors[] = 'Téléphone requis';
    if (empty($service)) $errors[] = 'Service requis';
    if (empty($message)) $errors[] = 'Message requis';

    if (count($errors) > 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Validation échouée',
            'errors' => $errors
        ]);
        exit;
    }

    // Essayer la base de données
    try {
        require_once 'php/config.php';
        
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Créer la table avec le bon schema
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `messages_contact` (
              `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `nom`         VARCHAR(100)     NOT NULL,
              `telephone`   VARCHAR(30)      NOT NULL,
              `email`       VARCHAR(150)     NULL,
              `service`     VARCHAR(50)      NOT NULL,
              `message`     TEXT             NOT NULL,
              `ip_adresse`  VARCHAR(45)      NULL,
              `date_envoi`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `statut`      VARCHAR(20)      NOT NULL DEFAULT 'nouveau',
              INDEX idx_date (date_envoi),
              INDEX idx_statut (statut)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Insérer le message
        $ip_raw  = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
        $ip_addr = inet_pton($ip_raw) !== false ? $ip_raw : 'inconnue';

        $stmt = $pdo->prepare("
            INSERT INTO messages_contact
                (nom, telephone, email, service, message, ip_adresse, date_envoi, statut)
            VALUES
                (:nom, :telephone, :email, :service, :message, :ip, NOW(), 'nouveau')
        ");

        $stmt->execute([
            ':nom'       => $nom,
            ':telephone' => $telephone,
            ':email'     => $email ?: null,
            ':service'   => $service,
            ':message'   => $message,
            ':ip'        => $ip_addr,
        ]);
        
        $message_id = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Message sauvegardé avec succès!',
            'messageId' => $message_id
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage(),
            'debug' => [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requête non-POST'
    ]);
}
?>
