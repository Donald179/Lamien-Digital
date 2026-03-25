<?php
/**
 * TechBurk — Mailer avec Gmail SMTP
 * mailer-smtp.php
 * 
 * Envoie les emails via Gmail SMTP (fonctionne partout)
 * Nécessite une "App Password" Google
 */

class TechBurkMailerSMTP {
    private $admin_email;
    private $site_name;
    private $smtp_host = 'smtp.gmail.com';
    private $smtp_port = 587;
    private $smtp_user;  // Votre email Gmail
    private $smtp_pass;  // Votre app password
    private $log_file;

    public function __construct(
        $admin_email = 'lamiendonaldo179@gmail.com',
        $site_name = 'LAMIEN DIGITAL',
        $smtp_user = null,
        $smtp_pass = null
    ) {
        $this->admin_email = $admin_email;
        $this->site_name = $site_name;
        $this->smtp_user = $smtp_user || $admin_email;
        $this->smtp_pass = $smtp_pass;
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
    public function sendContactNotification(
        $nom, $telephone, $email, $service, $message, $message_id, $ip_addr
    ) {
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
        
        // Créer les headers SMTP
        $from = trim($this->smtp_user);
        $to = trim($this->admin_email);
        $reply_to = !empty($email) ? trim($email) : $from;

        // Essayer d'envoyer via SMTP
        $sent = $this->sendViaSMTP($from, $to, $reply_to, $subject, $body);
        
        // Logging
        $this->logEmail($sent, $nom, $telephone, $email, $message_id);
        
        return $sent;
    }

    /**
     * Envoyer via SMTP
     */
    private function sendViaSMTP($from, $to, $reply_to, $subject, $body) {
        // Vérifier les paramètres requis
        if (empty($this->smtp_pass)) {
            error_log('[TechBurk SMTP] Erreur: smtp_pass non configuré');
            return false;
        }

        try {
            // Connexion SSL au serveur SMTP
            $connection = @fsockopen(
                'ssl://' . $this->smtp_host,
                $this->smtp_port,
                $errno,
                $errstr,
                10
            );

            if (!$connection) {
                error_log("[TechBurk SMTP] Erreur de connexion: $errno - $errstr");
                return false;
            }

            // Lire le message de bienvenue du serveur
            $response = fgets($connection, 1024);

            // EHLO
            fputs($connection, "EHLO " . $_SERVER['SERVER_NAME'] . "\r\n");
            $response = $this->getResponse($connection);

            // AUTH LOGIN
            fputs($connection, "AUTH LOGIN\r\n");
            $response = fgets($connection, 1024);

            // Encoder l'utilisateur en base64
            fputs($connection, base64_encode($this->smtp_user) . "\r\n");
            $response = fgets($connection, 1024);

            // Encoder le mot de passe en base64
            fputs($connection, base64_encode($this->smtp_pass) . "\r\n");
            $response = fgets($connection, 1024);

            if (strpos($response, '235') === false) {
                error_log('[TechBurk SMTP] Authentification échouée');
                fclose($connection);
                return false;
            }

            // MAIL FROM
            fputs($connection, "MAIL FROM:<$from>\r\n");
            $response = fgets($connection, 1024);

            // RCPT TO
            fputs($connection, "RCPT TO:<$to>\r\n");
            $response = fgets($connection, 1024);

            // DATA
            fputs($connection, "DATA\r\n");
            $response = fgets($connection, 1024);

            // Construire l'email
            $headers = "From: $from\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "Reply-To: $reply_to\r\n";
            $headers .= "Subject: " . $this->utf8Header($subject) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $headers .= "X-Mailer: TechBurkMailer/2.0 (SMTP)\r\n";
            $headers .= "X-Priority: 1 (Highest)\r\n";

            $email_data = $headers . "\r\n" . $body;

            // Envoyer l'email
            fputs($connection, $email_data . "\r\n.\r\n");
            $response = fgets($connection, 1024);

            if (strpos($response, '250') === false) {
                error_log('[TechBurk SMTP] Erreur lors de l\'envoi: ' . trim($response));
                fclose($connection);
                return false;
            }

            // QUIT
            fputs($connection, "QUIT\r\n");
            fclose($connection);

            return true;

        } catch (Exception $e) {
            error_log('[TechBurk SMTP] Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lire la réponse du serveur SMTP
     */
    private function getResponse($connection) {
        $response = '';
        while (@$line = fgets($connection, 1024)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        return $response;
    }

    /**
     * Encoder en UTF-8 pour le header SMTP
     */
    private function utf8Header($string) {
        return '=?UTF-8?B?' . base64_encode($string) . '?=';
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
}
?>
