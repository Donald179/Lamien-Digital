-- ============================================================
--  TechBurk — Expert Informatique Burkina Faso
--  Script SQL — Base de données complète
--  Version : 1.0
--  Moteur  : MySQL 5.7+ / MariaDB 10.3+
-- ============================================================
--
--  Instructions d'installation :
--  1. Ouvrez phpMyAdmin (http://localhost/phpmyadmin)
--  2. Cliquez sur "Importer"
--  3. Sélectionnez ce fichier et cliquez "Exécuter"
--  OU via terminal : mysql -u root -p < database.sql
-- ============================================================

-- Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS `techburk_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `techburk_db`;

-- ============================================================
--  TABLE : messages_contact
--  Stocke tous les messages reçus via le formulaire
-- ============================================================
CREATE TABLE IF NOT EXISTS `messages_contact` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `nom`         VARCHAR(100)     NOT NULL COMMENT 'Nom complet du client',
  `telephone`   VARCHAR(30)      NOT NULL COMMENT 'Numéro de téléphone',
  `email`       VARCHAR(150)     NULL     COMMENT 'Email (optionnel)',
  `service`     ENUM(
                  'maintenance',
                  'reparation',
                  'installation',
                  'vente',
                  'diagnostic',
                  'reseau',
                  'autre'
                )                NOT NULL COMMENT 'Service demandé',
  `message`     TEXT             NOT NULL COMMENT 'Contenu du message',
  `ip_adresse`  VARCHAR(45)      NULL     COMMENT 'Adresse IP (IPv4/IPv6)',
  `date_envoi`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut`      ENUM(
                  'nouveau',
                  'lu',
                  'en_traitement',
                  'traite',
                  'archive'
                )                NOT NULL DEFAULT 'nouveau' COMMENT 'Statut de traitement',
  `notes_admin` TEXT             NULL     COMMENT 'Notes internes (admin)',
  `updated_at`  DATETIME         NULL     ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  INDEX `idx_statut`      (`statut`),
  INDEX `idx_service`     (`service`),
  INDEX `idx_date_envoi`  (`date_envoi`),
  INDEX `idx_telephone`   (`telephone`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Messages reçus via le formulaire de contact';

-- ============================================================
--  TABLE : produits
--  Catalogue de la boutique (pour une future admin)
-- ============================================================
CREATE TABLE IF NOT EXISTS `produits` (
  `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `nom`           VARCHAR(150)   NOT NULL,
  `description`   TEXT           NULL,
  `categorie`     ENUM('laptop','desktop','accessoire','piece') NOT NULL,
  `prix`          DECIMAL(10,0)  NOT NULL COMMENT 'Prix en FCFA',
  `etat`          ENUM('neuf','reconditionne','occasion') NOT NULL DEFAULT 'neuf',
  `stock`         SMALLINT       NOT NULL DEFAULT 0,
  `image_url`     VARCHAR(300)   NULL,
  `actif`         TINYINT(1)     NOT NULL DEFAULT 1,
  `created_at`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME       NULL     ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  INDEX `idx_categorie` (`categorie`),
  INDEX `idx_actif`     (`actif`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catalogue produits de la boutique';

-- ============================================================
--  TABLE : temoignages
--  Témoignages clients (gestion dynamique future)
-- ============================================================
CREATE TABLE IF NOT EXISTS `temoignages` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nom`         VARCHAR(100)  NOT NULL,
  `poste`       VARCHAR(100)  NULL     COMMENT 'Ex: Particulier, Gérant PME...',
  `texte`       TEXT          NOT NULL,
  `note`        TINYINT       NOT NULL DEFAULT 5 COMMENT 'Note sur 5',
  `approuve`    TINYINT(1)    NOT NULL DEFAULT 0 COMMENT '1 = approuvé et visible',
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  INDEX `idx_approuve` (`approuve`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Témoignages clients';

-- ============================================================
--  TABLE : admin_users
--  Accès à un futur tableau de bord d'administration
-- ============================================================
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(50)   NOT NULL UNIQUE,
  `email`         VARCHAR(150)  NOT NULL UNIQUE,
  `password_hash` VARCHAR(255)  NOT NULL COMMENT 'bcrypt hash',
  `nom_complet`   VARCHAR(100)  NULL,
  `role`          ENUM('admin','super_admin') NOT NULL DEFAULT 'admin',
  `actif`         TINYINT(1)    NOT NULL DEFAULT 1,
  `derniere_connexion` DATETIME NULL,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Utilisateurs administrateurs';

-- ============================================================
--  DONNÉES D'EXEMPLE
-- ============================================================

-- Produits d'exemple
INSERT INTO `produits` (`nom`, `description`, `categorie`, `prix`, `etat`, `stock`) VALUES
('Laptop HP ProBook 450 G8',   'Intel Core i5 11e gen, 8GB RAM, SSD 256GB, Écran 15.6", Windows 11 Pro', 'laptop',    350000, 'neuf',          3),
('Laptop Dell Latitude 5490',  'Intel Core i7, 16GB RAM, SSD 512GB, Écran 14", Windows 10 Pro',           'laptop',    280000, 'reconditionne', 2),
('PC Bureau Assemblé Core i3', 'Intel Core i3, 8GB RAM, HDD 1TB, Tour + Écran 20", Windows 11',           'desktop',   200000, 'neuf',          5),
('Pack Clavier + Souris USB',  'Clavier AZERTY filaire + Souris optique USB, compatible Windows/Linux',    'accessoire',  8500, 'neuf',         20),
('Clé Wi-Fi USB 300Mbps',      'Adaptateur Wi-Fi USB 2.0, plug & play, compatible Win 7/8/10/11',         'accessoire',  7000, 'neuf',         15),
('Disque Dur Externe 1TB',     'Western Digital, USB 3.0, compatible PC & Mac, livré avec câble',         'accessoire', 35000, 'neuf',          8),
('Mémoire RAM DDR4 8GB',       'Barrette RAM 8GB DDR4 2666MHz, compatible la plupart des laptops/PC',     'piece',      25000, 'neuf',         10),
('SSD 2.5" 256GB',             'SATA SSD, temps de démarrage ultra-rapide, compatible tous PC',           'piece',      22000, 'neuf',          7);

-- Témoignages d'exemple
INSERT INTO `temoignages` (`nom`, `poste`, `texte`, `note`, `approuve`) VALUES
('Aminata Kaboré',  'Particulière, Ouagadougou',
 'Mon ordinateur était complètement bloqué. En moins de 2 heures, tout était réglé. Service rapide et professionnel !',
 5, 1),
('Boukari Traoré',  'Gérant PME, Secteur 15',
 "J'ai fait appel à lui pour l'installation du réseau de notre bureau. Travail impeccable, prix correct. Je recommande sans hésiter !",
 5, 1),
('Salimata Rabo',   'Étudiante, Université de Ouagadougou',
 "Récupération de mes données après une panne disque dur. J'avais perdu espoir mais il a tout récupéré. Merci mille fois !",
 4, 1),
('Ibrahim Sawadogo','Responsable IT, ONG',
 'Installation et configuration du réseau de notre bureau. Très professionnel, ponctuel et à l'écoute. Nous faisons appel à lui régulièrement.',
 5, 1);

-- Administrateur par défaut (mot de passe : Admin@2024 — À CHANGER !)
-- Hash bcrypt généré avec password_hash('Admin@2024', PASSWORD_BCRYPT)
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `nom_complet`, `role`) VALUES
('admin', 'contact@techburk.bf',
 '$2y$12$XhRc5nWVCKEK3JyT8pVxse6VNz7jV2Ac1F/CXZ/oZ8bJivyxnCVUu',
 'TechBurk Admin', 'super_admin');

-- ============================================================
--  VUES UTILES
-- ============================================================

-- Vue : messages non lus
CREATE OR REPLACE VIEW `v_messages_non_lus` AS
SELECT
  id, nom, telephone, service, message, date_envoi
FROM `messages_contact`
WHERE statut = 'nouveau'
ORDER BY date_envoi DESC;

-- Vue : statistiques des messages par service
CREATE OR REPLACE VIEW `v_stats_services` AS
SELECT
  service,
  COUNT(*) AS total_messages,
  SUM(CASE WHEN statut = 'traite' THEN 1 ELSE 0 END) AS messages_traites,
  MIN(date_envoi) AS premier_message,
  MAX(date_envoi) AS dernier_message
FROM `messages_contact`
GROUP BY service
ORDER BY total_messages DESC;

-- ============================================================
--  PROCÉDURE : Marquer un message comme lu
-- ============================================================
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `marquer_comme_lu`(IN p_id INT)
BEGIN
  UPDATE `messages_contact`
  SET statut = 'lu'
  WHERE id = p_id AND statut = 'nouveau';
END //
DELIMITER ;

-- ============================================================
--  RÉSUMÉ
-- ============================================================
SELECT '✅ Base de données TechBurk créée avec succès !' AS message;
SELECT CONCAT('   Tables créées : messages_contact, produits, temoignages, admin_users') AS info;
SELECT CONCAT('   Données d''exemple insérées') AS info2;
