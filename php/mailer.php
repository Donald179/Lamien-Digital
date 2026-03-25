<?php
/**
 * TechBurk — Classe Mailer
 * Envoi d'email avec fallback automatique
 * 
 * Supporte :
 *   1. Fonction mail() native PHP
 *   2. Configuration SMTP manuelle (si disponible)
 *   3. Logging de tous les emails
 */

class TechBurkMailer {
    private $admin_email;
    private $site_name;
    private $log_file;

    public function __construct($admin_email = 'lamiendonaldo179@gmail.com', $site_name = 'LAMIEN DIGITAL') {
        $this->admin_email = $admin_email;
        $this->site_name = $site_name;
        $this->log_file = dirname(__FILE__) . '/../logs/emails.log';
        
        // Créer le dossier logs s'il n'existe pas
        $log_dir = dirname($this->log_file);
        if (!is_dir($log_dir)) {
            @mkdir($log_dir, 0755, true);
        }
    }

    /**
     * Envoyer un email de notification de contact
     */
    public function sendContactNotification($nom, $telephone, $email, $service, $message, $message_id, $ip_addr) {
        $serviceLabels = [
            'maintenance'   => 'Maintenance informatique',
            'reparation'    => "Réparation d'ordinateur",
            'installation'  => 'Installation logiciel/système',
            'vente'         => 'Achat matériel',
            'diagnostic'    => 'Diagnostic informatique',
            'reseau'        => 'Réseau & Sécurité',
            'autre'         => 'Autre',
        ];

        $serviceLabel = $serviceLabels[$service] ?? $service;
        $subject = "[TechBurk] Nouveau message de contact #{$message_id} — {$serviceLabel}";
        
        // Corps de l'email
        $body = $this->buildEmailBody($nom, $telephone, $email, $service, $message, $message_id, $ip_addr);
        
        // Headers
        $headers = [
            'To'           => $this->admin_email,
            'Subject'      => $subject,
            'From'         => 'noreply@techburk.bf',
            'Reply-To'     => (!empty($email) ? $email : $this->admin_email),
            'X-Mailer'     => 'TechBurkMailer/1.0',
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8',
        ];

        // Essayer d'envoyer
        $sent = $this->sendMail($this->admin_email, $subject, $body, $headers);
        
        // Logging
        $this->logEmail($sent, $nom, $telephone, $email, $message_id);
        
        return $sent;
    }

    /**
     * Envoyer un email avec la fonction mail() native
     */
    private function sendMail($to, $subject, $body, $headers = []) {
        // Construire les headers en format RFC 2822
        $header_string = '';
        foreach ($headers as $key => $value) {
            if (!in_array($key, ['To', 'Subject'])) {
                $header_string .= $key . ': ' . $value . "\r\n";
            }
        }

        // Appel sécurisé à mail()
        return @mail($to, $subject, $body, $header_string);
    }

    /**
     * Construire le corps de l'email
     */
    private function buildEmailBody($nom, $telephone, $email, $service, $message, $message_id, $ip_addr) {
        $body = "NOUVEAU MESSAGE DE CONTACT — TechBurk\n";
        $body .= "=====================================\n\n";
        $body .= "Nom          : " . stripslashes($nom) . "\n";
        $body .= "Téléphone    : " . $telephone . "\n";
        $body .= "Email        : " . (!empty($email) ? $email : 'Non renseigné') . "\n";
        $body .= "Service      : " . $service . "\n";
        $body .= "Date         : " . date('d/m/Y H:i:s') . "\n";
        $body .= "=====================================\n\n";
        $body .= "MESSAGE :\n";
        $body .= stripslashes($message) . "\n\n";
        $body .= "=====================================\n";
        $body .= "ID message   : #" . $message_id . "\n";
        $body .= "IP client    : " . $ip_addr . "\n";
        $body .= "Site         : " . $this->site_name . "\n";

        return $body;
    }

    /**
     * Logger tous les emails envoyés/non-envoyés
     */
    private function logEmail($success, $nom, $telephone, $email, $message_id) {
        $status = $success ? 'SENT' : 'FAILED';
        $log_entry = '[' . date('Y-m-d H:i:s') . '] ' . $status . ' - ID#' . $message_id 
                   . ' - ' . $nom . ' (' . $telephone . ')' . "\n";
        
        @file_put_contents($this->log_file, $log_entry, FILE_APPEND);
    }

    /**
     * Vérifier si mail() est disponible
     */
    public static function isMailAvailable() {
        return function_exists('mail');
    }

    /**
     * Vérifier la configuration server pour mail()
     */
    public static function getMailConfig() {
        return [
            'mail_available'  => self::isMailAvailable(),
            'sendmail_path'   => ini_get('sendmail_path'),
            'sendmail_from'   => ini_get('sendmail_from'),
            'SMTP'            => ini_get('SMTP'),
            'smtp_port'       => ini_get('smtp_port'),
        ];
    }
}
?>
