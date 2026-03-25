<?php
/**
 * TechBurk — Expert Informatique Burkina Faso
 * contact.php — Traitement du formulaire de contact
 *
 * Fonctionnalités :
 *   - Validation des données
 *   - Enregistrement en base MySQL
 *   - Envoi d'email de notification
 *   - Réponse JSON
 */

// ─── CONFIGURATION ──────────────────────────────────────────
define('DB_HOST',     'localhost');
define('DB_NAME',     'techburk_db');
define('DB_USER',     'root');       // Modifier selon votre config XAMPP
define('DB_PASS',     '');           // Laisser vide pour XAMPP par défaut
define('DB_CHARSET',  'utf8mb4');

define('ADMIN_EMAIL', 'lamiendonaldo179@gmail.com'); // Votre adresse email
define('SITE_NAME',   'LAMIEN DIGITAL');

// Inclure la classe Mailer
require_once 'mailer.php';

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

/**
 * Nettoyer et sécuriser les données entrantes
 */
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Valider un numéro de téléphone (format Burkina Faso)
 */
function validatePhone(string $phone): bool {
    // Accepte formats : +22600000000, 00000000, 0000 0000, etc.
    $clean = preg_replace('/[\s\-\(\)\.]+/', '', $phone);
    return (bool) preg_match('/^(\+226|00226|226)?[0-9]{8}$/', $clean);
}

/**
 * Valider une adresse email
 */
function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Connexion PDO sécurisée
 */
function getDbConnection(): PDO {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $options);
}

// ─── RÉCUPÉRATION ET VALIDATION ─────────────────────────────

$errors = [];

// Nom
$nom = sanitize($_POST['nom'] ?? '');
if (empty($nom)) {
    $errors[] = 'Le nom est obligatoire.';
} elseif (strlen($nom) < 2 || strlen($nom) > 100) {
    $errors[] = 'Le nom doit contenir entre 2 et 100 caractères.';
}

// Téléphone
$telephone = sanitize($_POST['telephone'] ?? '');
if (empty($telephone)) {
    $errors[] = 'Le téléphone est obligatoire.';
} elseif (!validatePhone($telephone)) {
    $errors[] = 'Numéro de téléphone invalide.';
}

// Email (optionnel)
$email = sanitize($_POST['email'] ?? '');
if (!empty($email) && !validateEmail($email)) {
    $errors[] = 'Adresse email invalide.';
}

// Service
$services_valides = ['maintenance', 'reparation', 'installation', 'vente', 'diagnostic', 'reseau', 'autre'];
$service = sanitize($_POST['service'] ?? '');
if (empty($service) || !in_array($service, $services_valides)) {
    $errors[] = 'Veuillez sélectionner un service valide.';
}

// Message
$message = sanitize($_POST['message'] ?? '');
if (empty($message)) {
    $errors[] = 'Le message est obligatoire.';
} elseif (strlen($message) < 10) {
    $errors[] = 'Le message est trop court (minimum 10 caractères).';
} elseif (strlen($message) > 2000) {
    $errors[] = 'Le message est trop long (maximum 2000 caractères).';
}

// Protection anti-spam (honeypot, optionnel)
if (!empty($_POST['website'] ?? '')) {
    // Champ honeypot rempli → probablement un bot
    echo json_encode(['success' => true]); // Fausse réponse positive
    exit;
}

// S'il y a des erreurs, retourner
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

    // Obtenir l'adresse IP du client (masquée pour la confidentialité)
    $ip_raw  = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
    $ip_addr = inet_pton($ip_raw) !== false ? $ip_raw : 'inconnue';

    // Préparer la requête d'insertion
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

    // ─── ENVOI D'EMAIL DE NOTIFICATION ──────────────────────
    try {
        $mailer = new TechBurkMailer(ADMIN_EMAIL, SITE_NAME);
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
        error_log('[TechBurk Contact] Erreur Mailer : ' . $e->getMessage());
        $emailSent = false;
    }

    // ─── SUCCÈS ─────────────────────────────────────────────
    echo json_encode([
        'success'   => true,
        'message'   => 'Votre message a bien été envoyé ! Je vous contacte très bientôt.',
        'messageId' => $insertId
    ]);

} catch (PDOException $e) {
    // Journaliser l'erreur (ne pas exposer les détails)
    error_log('[TechBurk Contact] Erreur BDD : ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur. Veuillez nous contacter directement sur WhatsApp.'
    ]);
}
?>
