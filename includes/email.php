<?php
/**
 * Classe de Email com PHPMailer
 * Sistema completo de envio de emails
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Email {
    private $mailer;
    private $db;
    private $useQueue;
    
    /**
     * Construtor
     */
    public function __construct($useQueue = true) {
        $this->mailer = new PHPMailer(true);
        $this->db = Database::getInstance();
        $this->useQueue = $useQueue;
        
        $this->configure();
    }
    
    /**
     * Configurar PHPMailer com credenciais do .env
     */
    private function configure() {
        try {
            // Configurações do servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = getenv('SMTP_USER') ?: '';
            $this->mailer->Password = getenv('SMTP_PASS') ?: '';
            $this->mailer->SMTPSecure = getenv('SMTP_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = getenv('SMTP_PORT') ?: 587;
            $this->mailer->CharSet = 'UTF-8';
            
            // Remetente padrão
            $fromEmail = getenv('MAIL_FROM_ADDRESS') ?: 'noreply@devmenthors.com';
            $fromName = getenv('MAIL_FROM_NAME') ?: 'DevMenthors';
            $this->mailer->setFrom($fromEmail, $fromName);
            
        } catch (Exception $e) {
            error_log("Erro ao configurar PHPMailer: " . $e->getMessage());
        }
    }
    
    /**
     * Enviar email de boas-vindas
     */
    public function sendWelcome($toEmail, $toName, $planName) {
        $subject = "Bem-vindo ao DevMenthors! 🎉";
        $template = $this->getTemplate('welcome', [
            'name' => $toName,
            'plan_name' => $planName,
            'login_url' => getenv('BASE_URL') . '/login.php',
            'dashboard_url' => getenv('BASE_URL') . '/dashboard/'
        ]);
        
        return $this->send($toEmail, $toName, $subject, $template);
    }
    
    /**
     * Enviar email de verificação
     */
    public function sendVerification($toEmail, $toName, $verificationToken) {
        $subject = "Verifique seu email - DevMenthors";
        $verifyUrl = getenv('BASE_URL') . '/verify-email.php?token=' . $verificationToken;
        
        $template = $this->getTemplate('verify-email', [
            'name' => $toName,
            'verify_url' => $verifyUrl,
            'token' => $verificationToken
        ]);
        
        return $this->send($toEmail, $toName, $subject, $template);
    }
    
    /**
     * Enviar email de recuperação de senha
     */
    public function sendPasswordReset($toEmail, $toName, $resetToken) {
        $subject = "Recuperação de senha - DevMenthors";
        $resetUrl = getenv('BASE_URL') . '/reset-password.php?token=' . $resetToken;
        
        $template = $this->getTemplate('reset-password', [
            'name' => $toName,
            'reset_url' => $resetUrl,
            'token' => $resetToken
        ]);
        
        return $this->send($toEmail, $toName, $subject, $template);
    }
    
    /**
     * Enviar email de pagamento confirmado
     */
    public function sendPaymentConfirmed($toEmail, $toName, $planName, $amount, $transactionId) {
        $subject = "Pagamento confirmado! 🎉";
        $template = $this->getTemplate('payment-confirmed', [
            'name' => $toName,
            'plan_name' => $planName,
            'amount' => number_format($amount, 2, ',', '.'),
            'transaction_id' => $transactionId,
            'dashboard_url' => getenv('BASE_URL') . '/dashboard/',
            'invoice_url' => getenv('BASE_URL') . '/invoice.php?txn=' . $transactionId
        ]);
        
        return $this->send($toEmail, $toName, $subject, $template);
    }
    
    /**
     * Enviar email de assinatura expirando
     */
    public function sendSubscriptionExpiring($toEmail, $toName, $planName, $daysLeft, $endDate) {
        $subject = "Sua assinatura expira em {$daysLeft} dias ⏰";
        $template = $this->getTemplate('subscription-expiring', [
            'name' => $toName,
            'plan_name' => $planName,
            'days_left' => $daysLeft,
            'end_date' => date('d/m/Y', strtotime($endDate)),
            'renew_url' => getenv('BASE_URL') . '/dashboard/subscription.php',
            'support_url' => getenv('BASE_URL') . '/dashboard/support.php'
        ]);
        
        return $this->send($toEmail, $toName, $subject, $template);
    }
    
    /**
     * Enviar email genérico
     */
    public function send($toEmail, $toName, $subject, $htmlBody, $attachments = []) {
        // Se usar fila, adicionar à fila ao invés de enviar diretamente
        if ($this->useQueue) {
            return $this->addToQueue($toEmail, $toName, $subject, $htmlBody, $attachments);
        }
        
        // Enviar diretamente
        return $this->sendNow($toEmail, $toName, $subject, $htmlBody, $attachments);
    }
    
    /**
     * Enviar email imediatamente (sem fila)
     */
    private function sendNow($toEmail, $toName, $subject, $htmlBody, $attachments = []) {
        try {
            // Limpar destinatários anteriores
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            // Destinatário
            $this->mailer->addAddress($toEmail, $toName);
            
            // Assunto e corpo
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = strip_tags($htmlBody);
            
            // Anexos
            foreach ($attachments as $attachment) {
                if (isset($attachment['path']) && file_exists($attachment['path'])) {
                    $this->mailer->addAttachment(
                        $attachment['path'],
                        $attachment['name'] ?? basename($attachment['path'])
                    );
                }
            }
            
            // Enviar
            $result = $this->mailer->send();
            
            // Log de sucesso
            $this->logEmail($toEmail, $subject, 'sent', null);
            
            return true;
            
        } catch (Exception $e) {
            // Log de erro
            $errorMsg = $this->mailer->ErrorInfo;
            $this->logEmail($toEmail, $subject, 'failed', $errorMsg);
            
            error_log("Erro ao enviar email: " . $errorMsg);
            return false;
        }
    }
    
    /**
     * Adicionar email à fila
     */
    private function addToQueue($toEmail, $toName, $subject, $htmlBody, $attachments = []) {
        try {
            $sql = "INSERT INTO email_queue (to_email, to_name, subject, body, attachments, status, created_at)
                    VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
            
            $attachmentsJson = json_encode($attachments);
            
            $this->db->query($sql, [
                $toEmail,
                $toName,
                $subject,
                $htmlBody,
                $attachmentsJson
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Erro ao adicionar email à fila: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Processar fila de emails (executar via cron)
     */
    public function processQueue($limit = 10) {
        try {
            // Buscar emails pendentes
            $sql = "SELECT * FROM email_queue 
                    WHERE status = 'pending' 
                    AND (scheduled_at IS NULL OR scheduled_at <= NOW())
                    ORDER BY created_at ASC 
                    LIMIT ?";
            
            $emails = $this->db->fetchAll($sql, [$limit]);
            
            $sent = 0;
            $failed = 0;
            
            foreach ($emails as $email) {
                // Marcar como processando
                $this->db->query(
                    "UPDATE email_queue SET status = 'processing' WHERE id = ?",
                    [$email['id']]
                );
                
                // Decodificar anexos
                $attachments = json_decode($email['attachments'], true) ?: [];
                
                // Tentar enviar
                $success = $this->sendNow(
                    $email['to_email'],
                    $email['to_name'],
                    $email['subject'],
                    $email['body'],
                    $attachments
                );
                
                if ($success) {
                    // Marcar como enviado
                    $this->db->query(
                        "UPDATE email_queue SET status = 'sent', sent_at = NOW() WHERE id = ?",
                        [$email['id']]
                    );
                    $sent++;
                } else {
                    // Incrementar tentativas e marcar como falha
                    $attempts = $email['attempts'] + 1;
                    $maxAttempts = 3;
                    
                    if ($attempts >= $maxAttempts) {
                        $this->db->query(
                            "UPDATE email_queue SET status = 'failed', attempts = ?, error = 'Max attempts reached' WHERE id = ?",
                            [$attempts, $email['id']]
                        );
                    } else {
                        // Retentar depois (exponential backoff)
                        $retryAfter = pow(2, $attempts); // 2, 4, 8 minutos
                        $this->db->query(
                            "UPDATE email_queue SET status = 'pending', attempts = ?, scheduled_at = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?",
                            [$attempts, $retryAfter, $email['id']]
                        );
                    }
                    $failed++;
                }
                
                // Pequeno delay para não sobrecarregar SMTP
                usleep(500000); // 0.5 segundos
            }
            
            return [
                'processed' => count($emails),
                'sent' => $sent,
                'failed' => $failed
            ];
            
        } catch (Exception $e) {
            error_log("Erro ao processar fila de emails: " . $e->getMessage());
            return [
                'processed' => 0,
                'sent' => 0,
                'failed' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obter template de email
     */
    private function getTemplate($templateName, $variables = []) {
        $templatePath = __DIR__ . "/../templates/email/{$templateName}.html";
        
        if (!file_exists($templatePath)) {
            throw new Exception("Template não encontrado: {$templateName}");
        }
        
        $html = file_get_contents($templatePath);
        
        // Substituir variáveis
        foreach ($variables as $key => $value) {
            $html = str_replace("{{" . $key . "}}", $value, $html);
        }
        
        // Variáveis globais
        $html = str_replace("{{year}}", date('Y'), $html);
        $html = str_replace("{{base_url}}", getenv('BASE_URL'), $html);
        $html = str_replace("{{company_name}}", 'DevMenthors', $html);
        
        return $html;
    }
    
    /**
     * Registrar log de email
     */
    private function logEmail($toEmail, $subject, $status, $error = null) {
        try {
            $sql = "INSERT INTO email_logs (to_email, subject, status, error, created_at)
                    VALUES (?, ?, ?, ?, NOW())";
            
            $this->db->query($sql, [$toEmail, $subject, $status, $error]);
            
        } catch (Exception $e) {
            error_log("Erro ao registrar log de email: " . $e->getMessage());
        }
    }
    
    /**
     * Testar configuração de email
     */
    public function testConnection() {
        try {
            $this->mailer->SMTPDebug = 0;
            $result = $this->mailer->smtpConnect();
            $this->mailer->smtpClose();
            
            return $result;
            
        } catch (Exception $e) {
            return false;
        }
    }
}
