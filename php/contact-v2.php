<?php
/**
 * TechBurk — Expert Informatique Burkina Faso
 * contact.php v2 — Avec support Gmail SMTP
 */

// ─── CONFIGURATION ──────────────────────────────────────────
require_once 'config.php';
require_once 'mailer-smtp.php';

// ─── HEADERS ────────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Accepter uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// ─── FONCTIONS UTILITAIRES ───────────────────────────────────

function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validatePhone(string $phone): bool {
    $clean = preg_replace('/[\s\-\(\)\.]+/', '', $phone);
    return (bool) preg_match('/^(\+226|00226|226)?[0-9]{8}$/', $clean);
}

function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function getDbConnection(): PDO {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $options);
}

// ─── RÉCUPÉRATION ET VALIDATION ─────────────────────────

$errors = [];

$nom = sanitize($_POST['nom'] ?? '');
if (empty($nom)) {
    $errors[] = 'Le nom est obligatoire.';
} elseif (strlen($nom) < 2 || strlen($nom) > 100) {
    $errors[] = 'Le nom doit contenir entre 2 et 100 caractères.';
}

$telephone = sanitize($_POST['telephone'] ?? '');
if (empty($telephone)) {
    $errors[] = 'Le téléphone est obligatoire.';
} elseif (!validatePhone($telephone)) {
    $errors[] = 'Numéro de téléphone invalide.';
}

$email = sanitize($_POST['email'] ?? '');
if (!empty($email) && !validateEmail($email)) {
    $errors[] = 'Adresse email invalide.';
}

$services_valides = ['maintenance', 'reparation', 'installation', 'vente', 'diagnostic', 'reseau', 'autre'];
$service = sanitize($_POST['service'] ?? '');
if (empty($service) || !in_array($service, $services_valides)) {
    $errors[] = 'Veuillez sélectionner un service valide.';
}

$message = sanitize($_POST['message'] ?? '');
if (empty($message)) {
    $errors[] = 'Le message est obligatoire.';
} elseif (strlen($message) < 10) {
    $errors[] = 'Le message est trop court (minimum 10 caractères).';
} elseif (strlen($message) > 2000) {
    $errors[] = 'Le message est trop long (maximum 2000 caractères).';
}

// Protection anti-spam
if (!empty($_POST['website'] ?? '')) {
    echo json_encode(['success' => true]);
    exit;
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors),
        'errors'  => $errors
    ]);
    exit;
}

// ─── ENREGISTREMENT EN BASE DE DONNÉES ──────────────────────

try {
    $pdo = getDbConnection();

    // Créer la table si elle n'existe pas
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

    $insertId = $pdo->lastInsertId();

    // ─── ENVOI D'EMAIL ──────────────────────────────────────

    $emailSent = false;

    if (SEND_EMAILS && !empty(SMTP_PASS)) {
        try {
            $mailer = new TechBurkMailerSMTP(
                ADMIN_EMAIL,
                SITE_NAME,
                SMTP_USER,
                SMTP_PASS
            );
            $emailSent = $mailer->sendContactNotification(
                $nom,
                $telephone,
                $email,
                $service,
                $message,
                $insertId,
                $ip_addr
            );
        } catch (Exception $e) {
            error_log('[TechBurk] Erreur envoi email: ' . $e->getMessage());
            $emailSent = false;
        }
    } else {
        if (!SEND_EMAILS) {
            error_log('[TechBurk] Envoi d\'email désactivé');
        } else {
            error_log('[TechBurk] SMTP_PASS vide - Emails désactivés');
        }
    }

    // ─── SUCCÈS ─────────────────────────────────────────────
    echo json_encode([
        'success'   => true,
        'message'   => 'Votre message a bien été envoyé ! Je vous contacte très bientôt.',
        'messageId' => $insertId,
        'emailSent' => $emailSent
    ]);

} catch (PDOException $e) {
    error_log('[TechBurk] Erreur BDD: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données.'
    ]);
} catch (Exception $e) {
    error_log('[TechBurk] Erreur générale: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur du serveur. Vérifiez la configuration.'
    ]);
}
?>
