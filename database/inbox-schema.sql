-- Tabelas para Sistema de Inbox

-- Tabela de emails recebidos
CREATE TABLE IF NOT EXISTS inbox_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id VARCHAR(255) UNIQUE,
    from_email VARCHAR(255) NOT NULL,
    from_name VARCHAR(255),
    subject TEXT,
    body LONGTEXT,
    category ENUM('cliente', 'spam', 'suporte', 'geral') DEFAULT 'geral',
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    attachments JSON,
    imap_id VARCHAR(50),
    received_at DATETIME,
    replied_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_from_email (from_email),
    INDEX idx_received_at (received_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de respostas enviadas
CREATE TABLE IF NOT EXISTS inbox_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email_id INT NOT NULL,
    reply_body LONGTEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (email_id) REFERENCES inbox_emails(id) ON DELETE CASCADE,
    INDEX idx_email_id (email_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
