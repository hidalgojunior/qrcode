<?php
/**
 * Classe InboxManager
 * Gerenciamento completo de caixa de entrada via IMAP
 * - Recebe emails automaticamente
 * - Categoriza mensagens (cliente, spam, suporte, geral)
 * - Permite responder emails
 * - Armazena no banco de dados
 */

use PhpImap\Mailbox;
use PhpImap\Exceptions\ConnectionException;

class InboxManager {
    private $db;
    private $mailbox;
    private $imapPath;
    private $username;
    private $password;
    
    // Configurações IMAP Hostinger
    private $imapHost = 'imap.hostinger.com';
    private $imapPort = 993;
    private $imapFlags = '/imap/ssl/validate-cert';
    
    // Configurações SMTP Hostinger
    private $smtpHost = 'smtp.hostinger.com';
    private $smtpPort = 465;
    private $smtpEncryption = 'ssl';
    
    // Palavras-chave para categorização
    private $clientKeywords = [
        'assinatura', 'plano', 'pagamento', 'renovar', 'upgrade', 'downgrade',
        'fatura', 'cobrança', 'qr code', 'microsite', 'suporte técnico',
        'não funciona', 'erro', 'problema', 'ajuda', 'como', 'tutorial'
    ];
    
    private $spamKeywords = [
        'ganhe dinheiro', 'clique aqui', 'promoção imperdível', 'você ganhou',
        'prêmio', 'loteria', 'urgente', 'última chance', 'oferta exclusiva'
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Carregar configurações do .env
        $this->username = getenv('INBOX_EMAIL') ?: 'contato@devmenthors.shop';
        $this->password = getenv('INBOX_PASSWORD') ?: '@Jr34139251';
        
        // Construir path IMAP
        $this->imapPath = sprintf(
            '{%s:%d%s}INBOX',
            $this->imapHost,
            $this->imapPort,
            $this->imapFlags
        );
    }
    
    /**
     * Conectar ao servidor IMAP
     */
    public function connect() {
        try {
            $this->mailbox = new Mailbox(
                $this->imapPath,
                $this->username,
                $this->password,
                __DIR__ . '/../attachments'
            );
            
            return true;
        } catch (ConnectionException $e) {
            error_log("Erro ao conectar IMAP: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar novos emails da caixa de entrada
     */
    public function fetchNewEmails($limit = 50) {
        if (!$this->connect()) {
            return ['success' => false, 'error' => 'Falha na conexão IMAP'];
        }
        
        try {
            // Buscar emails não lidos
            $mailIds = $this->mailbox->searchMailbox('UNSEEN');
            
            if (empty($mailIds)) {
                return [
                    'success' => true, 
                    'message' => 'Nenhum email novo', 
                    'processed' => 0,
                    'total' => 0,
                    'errors' => []
                ];
            }
            
            // Limitar quantidade
            $mailIds = array_slice($mailIds, 0, $limit);
            $processed = 0;
            $errors = [];
            
            foreach ($mailIds as $mailId) {
                try {
                    $mail = $this->mailbox->getMail($mailId);
                    
                    // Processar e salvar no banco
                    $saved = $this->saveEmail($mail, $mailId);
                    
                    if ($saved) {
                        $processed++;
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Erro no email ID $mailId: " . $e->getMessage();
                }
            }
            
            return [
                'success' => true,
                'processed' => $processed,
                'total' => count($mailIds),
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Salvar email no banco de dados
     */
    private function saveEmail($mail, $mailId) {
        try {
            // Extrair informações
            $from = $mail->fromAddress;
            $fromName = $mail->fromName ?: $from;
            $subject = $mail->subject ?: '(Sem assunto)';
            $body = $mail->textHtml ?: $mail->textPlain;
            $date = $mail->date;
            
            // Categorizar
            $category = $this->categorizeEmail($subject, $body, $from);
            
            // Verificar se já existe
            $stmt = $this->db->prepare("
                SELECT id FROM inbox_emails 
                WHERE message_id = ? OR (from_email = ? AND subject = ? AND received_at = ?)
            ");
            $stmt->bind_param('ssss', $mail->messageId, $from, $subject, $date);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                return false; // Email já existe
            }
            
            // Processar anexos
            $attachments = [];
            if ($mail->hasAttachments()) {
                foreach ($mail->getAttachments() as $attachment) {
                    $attachments[] = [
                        'name' => $attachment->name,
                        'size' => strlen($attachment->getContents()),
                        'type' => $attachment->mimeType,
                        'path' => $this->saveAttachment($attachment, $mail->messageId)
                    ];
                }
            }
            
            // Inserir no banco
            $stmt = $this->db->prepare("
                INSERT INTO inbox_emails (
                    message_id, from_email, from_name, subject, body, 
                    category, status, attachments, imap_id, received_at, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, 'unread', ?, ?, ?, NOW())
            ");
            
            $attachmentsJson = json_encode($attachments);
            $status = 'unread';
            
            $stmt->bind_param(
                'sssssssss',
                $mail->messageId,
                $from,
                $fromName,
                $subject,
                $body,
                $category,
                $attachmentsJson,
                $mailId,
                $date
            );
            
            return $stmt->execute();
            
        } catch (\Exception $e) {
            error_log("Erro ao salvar email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Categorizar email automaticamente
     */
    private function categorizeEmail($subject, $body, $from) {
        $text = strtolower($subject . ' ' . $body);
        
        // 1. Verificar se é spam
        foreach ($this->spamKeywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                return 'spam';
            }
        }
        
        // 2. Verificar se é de cliente (domínio conhecido ou conteúdo)
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $from);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            return 'cliente';
        }
        
        // 3. Verificar palavras-chave de cliente
        foreach ($this->clientKeywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                return 'cliente';
            }
        }
        
        // 4. Se menciona "suporte" ou "ajuda"
        if (strpos($text, 'suporte') !== false || strpos($text, 'ajuda') !== false) {
            return 'suporte';
        }
        
        // 5. Padrão: geral
        return 'geral';
    }
    
    /**
     * Salvar anexo no servidor
     */
    private function saveAttachment($attachment, $messageId) {
        $dir = __DIR__ . '/../attachments/' . date('Y-m-d');
        
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $filename = $messageId . '_' . $attachment->name;
        $filepath = $dir . '/' . $filename;
        
        file_put_contents($filepath, $attachment->getContents());
        
        return '/attachments/' . date('Y-m-d') . '/' . $filename;
    }
    
    /**
     * Listar emails da caixa de entrada
     */
    public function listEmails($filters = []) {
        $where = ['1=1'];
        $params = [];
        $types = '';
        
        // Filtro por categoria
        if (!empty($filters['category'])) {
            $where[] = 'category = ?';
            $params[] = $filters['category'];
            $types .= 's';
        }
        
        // Filtro por status
        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }
        
        // Filtro por busca
        if (!empty($filters['search'])) {
            $where[] = '(subject LIKE ? OR body LIKE ? OR from_email LIKE ? OR from_name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $types .= 'ssss';
        }
        
        // Paginação
        $page = isset($filters['page']) ? (int)$filters['page'] : 1;
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 20;
        $offset = ($page - 1) * $limit;
        
        // Query
        $sql = "SELECT * FROM inbox_emails WHERE " . implode(' AND ', $where) . " 
                ORDER BY received_at DESC LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $emails = [];
        while ($row = $result->fetch_assoc()) {
            $row['attachments'] = json_decode($row['attachments'], true);
            $emails[] = $row;
        }
        
        // Contar total
        $countSql = "SELECT COUNT(*) as total FROM inbox_emails WHERE " . implode(' AND ', $where);
        $countStmt = $this->db->prepare($countSql);
        
        if (!empty($params) && count($params) > 2) {
            $countParams = array_slice($params, 0, -2); // Remove limit e offset
            $countTypes = substr($types, 0, -2);
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        
        $countStmt->execute();
        $total = $countStmt->get_result()->fetch_assoc()['total'];
        
        return [
            'emails' => $emails,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ];
    }
    
    /**
     * Marcar email como lido
     */
    public function markAsRead($emailId) {
        $stmt = $this->db->prepare("UPDATE inbox_emails SET status = 'read' WHERE id = ?");
        $stmt->bind_param('i', $emailId);
        return $stmt->execute();
    }
    
    /**
     * Responder email
     */
    public function replyEmail($emailId, $replyBody, $attachments = []) {
        // Buscar email original
        $stmt = $this->db->prepare("SELECT * FROM inbox_emails WHERE id = ?");
        $stmt->bind_param('i', $emailId);
        $stmt->execute();
        $email = $stmt->get_result()->fetch_assoc();
        
        if (!$email) {
            return ['success' => false, 'error' => 'Email não encontrado'];
        }
        
        // Enviar resposta via PHPMailer
        require_once __DIR__ . '/email.php';
        
        $mailer = new Email(false); // Sem fila, envio direto
        
        $subject = 'Re: ' . $email['subject'];
        
        // Montar corpo da resposta
        $htmlBody = "
        <div style='font-family: Arial, sans-serif; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; color: white; margin-bottom: 20px;'>
                <h2 style='margin: 0;'>DevMenthors - Resposta</h2>
            </div>
            
            <div style='margin-bottom: 30px;'>
                {$replyBody}
            </div>
            
            <hr style='border: 1px solid #eee; margin: 30px 0;'>
            
            <div style='color: #666; font-size: 12px;'>
                <p><strong>Mensagem original de:</strong> {$email['from_name']} &lt;{$email['from_email']}&gt;</p>
                <p><strong>Data:</strong> " . date('d/m/Y H:i', strtotime($email['received_at'])) . "</p>
                <p><strong>Assunto:</strong> {$email['subject']}</p>
                <blockquote style='border-left: 3px solid #667eea; padding-left: 15px; margin: 20px 0;'>
                    {$email['body']}
                </blockquote>
            </div>
        </div>
        ";
        
        // Enviar
        $sent = $mailer->send(
            $email['from_email'],
            $email['from_name'],
            $subject,
            $htmlBody,
            $attachments
        );
        
        if ($sent) {
            // Marcar como respondido
            $stmt = $this->db->prepare("UPDATE inbox_emails SET status = 'replied', replied_at = NOW() WHERE id = ?");
            $stmt->bind_param('i', $emailId);
            $stmt->execute();
            
            // Salvar resposta
            $stmt = $this->db->prepare("
                INSERT INTO inbox_replies (email_id, reply_body, sent_at) 
                VALUES (?, ?, NOW())
            ");
            $stmt->bind_param('is', $emailId, $replyBody);
            $stmt->execute();
            
            return ['success' => true, 'message' => 'Resposta enviada com sucesso'];
        }
        
        return ['success' => false, 'error' => 'Falha ao enviar resposta'];
    }
    
    /**
     * Deletar email
     */
    public function deleteEmail($emailId) {
        $stmt = $this->db->prepare("DELETE FROM inbox_emails WHERE id = ?");
        $stmt->bind_param('i', $emailId);
        return $stmt->execute();
    }
    
    /**
     * Estatísticas da caixa de entrada
     */
    public function getStats() {
        $stats = [
            'by_status' => [],
            'by_category' => [],
            'total' => 0,
            'unread' => 0
        ];
        
        // Total por status
        $result = $this->db->query("
            SELECT status, COUNT(*) as count 
            FROM inbox_emails 
            GROUP BY status
        ");
        
        while ($row = $result->fetch()) {
            $stats['by_status'][$row['status']] = (int)$row['count'];
        }
        
        // Total por categoria
        $result = $this->db->query("
            SELECT category, COUNT(*) as count 
            FROM inbox_emails 
            GROUP BY category
        ");
        
        while ($row = $result->fetch()) {
            $stats['by_category'][$row['category']] = (int)$row['count'];
        }
        
        // Total geral
        $result = $this->db->query("SELECT COUNT(*) as total FROM inbox_emails");
        $row = $result->fetch();
        $stats['total'] = (int)($row['total'] ?? 0);
        
        // Não lidos
        $result = $this->db->query("SELECT COUNT(*) as unread FROM inbox_emails WHERE status = 'unread'");
        $row = $result->fetch();
        $stats['unread'] = (int)($row['unread'] ?? 0);
        
        return $stats;
    }
    
    /**
     * Testar conexão IMAP
     */
    public function testConnection() {
        try {
            if (!$this->connect()) {
                return ['success' => false, 'error' => 'Falha na conexão'];
            }
            
            $info = $this->mailbox->getMailboxInfo();
            
            return [
                'success' => true,
                'message' => 'Conexão IMAP estabelecida com sucesso',
                'info' => [
                    'mailbox' => $info->Mailbox,
                    'messages' => $info->Nmsgs,
                    'recent' => $info->Recent,
                    'unread' => $info->Unread
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
