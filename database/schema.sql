-- DevMenthors Database Schema
-- Criado em: 2025-10-01

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Tabela de Usuários
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `plan_id` int(11) DEFAULT 1,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `plan_id` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Planos
-- --------------------------------------------------------

CREATE TABLE `plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `microsites_limit` int(11) DEFAULT NULL COMMENT 'NULL = ilimitado',
  `qrcodes_limit` int(11) DEFAULT NULL COMMENT 'NULL = ilimitado',
  `widgets_limit` int(11) DEFAULT NULL COMMENT 'NULL = ilimitado',
  `has_watermark` tinyint(1) DEFAULT 0,
  `custom_domain` tinyint(1) DEFAULT 0,
  `priority_support` tinyint(1) DEFAULT 0,
  `api_access` tinyint(1) DEFAULT 0,
  `white_label` tinyint(1) DEFAULT 0,
  `features` text DEFAULT NULL COMMENT 'JSON com recursos adicionais',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir planos padrão
INSERT INTO `plans` (`name`, `slug`, `price`, `microsites_limit`, `qrcodes_limit`, `widgets_limit`, `has_watermark`, `custom_domain`, `priority_support`, `api_access`, `white_label`) VALUES
('Básico', 'basico', 10.00, 1, NULL, NULL, 1, 0, 0, 0, 0),
('Starter', 'starter', 20.00, 1, NULL, NULL, 0, 0, 1, 0, 0),
('Pro', 'pro', 70.00, 10, NULL, NULL, 0, 1, 1, 0, 0),
('Enterprise', 'enterprise', 0.00, NULL, NULL, NULL, 0, 1, 1, 1, 1);

-- Planos específicos de QR Code
INSERT INTO `plans` (`name`, `slug`, `price`, `microsites_limit`, `qrcodes_limit`, `widgets_limit`, `has_watermark`, `custom_domain`, `priority_support`, `api_access`, `white_label`) VALUES
('QR Code Grátis', 'qrcode-free', 0.00, 0, NULL, 0, 1, 0, 0, 0, 0),
('QR Code Starter', 'qrcode-starter', 20.00, 0, 10, 0, 0, 0, 0, 0, 0),
('QR Code Pro', 'qrcode-pro', 30.00, 0, 50, 0, 0, 0, 1, 0, 0),
('QR Code Enterprise', 'qrcode-enterprise', 0.00, 0, NULL, 0, 0, 0, 1, 1, 0);

-- --------------------------------------------------------
-- Tabela de Assinaturas
-- --------------------------------------------------------

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `status` enum('pending','active','cancelled','expired','suspended') DEFAULT 'pending',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `auto_renew` tinyint(1) DEFAULT 1,
  `payment_method` varchar(50) DEFAULT 'pix',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `plan_id` (`plan_id`),
  KEY `status` (`status`),
  CONSTRAINT `subscriptions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscriptions_plan_fk` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Pagamentos
-- --------------------------------------------------------

CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'pix',
  `status` enum('pending','paid','failed','refunded','cancelled') DEFAULT 'pending',
  `pix_code` text DEFAULT NULL,
  `pix_qrcode` text DEFAULT NULL,
  `pix_txid` varchar(100) DEFAULT NULL,
  `external_id` varchar(100) DEFAULT NULL COMMENT 'ID do gateway de pagamento',
  `paid_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `metadata` text DEFAULT NULL COMMENT 'JSON com dados adicionais',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `subscription_id` (`subscription_id`),
  KEY `status` (`status`),
  KEY `pix_txid` (`pix_txid`),
  CONSTRAINT `payments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_subscription_fk` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Microsites (migração dos arquivos JSON)
-- --------------------------------------------------------

CREATE TABLE `microsites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `theme` text DEFAULT NULL COMMENT 'JSON com configurações de tema',
  `widgets` text DEFAULT NULL COMMENT 'JSON com widgets',
  `views` int(11) DEFAULT 0,
  `status` enum('active','inactive','draft') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  CONSTRAINT `microsites_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de QR Codes
-- --------------------------------------------------------

CREATE TABLE `qrcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('text','url','email','phone','sms','wifi','vcard','microsite') NOT NULL,
  `content` text NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT 300,
  `color` varchar(7) DEFAULT '#000000',
  `bgcolor` varchar(7) DEFAULT '#ffffff',
  `margin` int(11) DEFAULT 1,
  `format` varchar(10) DEFAULT 'png',
  `filename` varchar(255) DEFAULT NULL,
  `downloads` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  CONSTRAINT `qrcodes_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Analytics
-- --------------------------------------------------------

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `microsite_id` int(11) NOT NULL,
  `visitor_ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referer` varchar(500) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `device` varchar(50) DEFAULT NULL,
  `browser` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `microsite_id` (`microsite_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `analytics_microsite_fk` FOREIGN KEY (`microsite_id`) REFERENCES `microsites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Notificações
-- --------------------------------------------------------

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  CONSTRAINT `notifications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Sessões
-- --------------------------------------------------------

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `sessions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Logs de Auditoria
-- --------------------------------------------------------

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `old_values` text DEFAULT NULL COMMENT 'JSON',
  `new_values` text DEFAULT NULL COMMENT 'JSON',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `entity_type` (`entity_type`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `audit_logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de Configurações do Sistema
-- --------------------------------------------------------

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(20) DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir configurações padrão
INSERT INTO `settings` (`key`, `value`, `type`, `description`) VALUES
('site_name', 'DevMenthors', 'string', 'Nome do site'),
('site_url', 'http://localhost/QrCode', 'string', 'URL base do site'),
('pix_key', '', 'string', 'Chave PIX para recebimentos'),
('pix_name', '', 'string', 'Nome do titular da conta PIX'),
('payment_gateway', 'manual', 'string', 'Gateway de pagamento (manual, mercadopago, pagseguro)'),
('mercadopago_public_key', '', 'string', 'Chave pública Mercado Pago'),
('mercadopago_access_token', '', 'string', 'Access token Mercado Pago'),
('email_from', 'noreply@devmenthors.com', 'string', 'Email de envio'),
('smtp_host', '', 'string', 'Servidor SMTP'),
('smtp_port', '587', 'string', 'Porta SMTP'),
('smtp_user', '', 'string', 'Usuário SMTP'),
('smtp_password', '', 'string', 'Senha SMTP'),
('max_microsites_per_user', '10', 'integer', 'Máximo de microsites por usuário'),
('allow_registration', '1', 'boolean', 'Permitir novos cadastros'),
('require_email_verification', '1', 'boolean', 'Requer verificação de email');

COMMIT;
