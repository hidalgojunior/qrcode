<?php
/**
 * API de Teste de Email
 */

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/email.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $action = $_GET['action'] ?? 'send';
    
    // Testar conexão SMTP
    if ($action === 'test-connection') {
        $email = new Email(false);
        $connected = $email->testConnection();
        
        echo json_encode([
            'success' => $connected,
            'message' => $connected ? 'Conexão SMTP OK!' : 'Falha na conexão SMTP. Verifique as credenciais.'
        ]);
        exit;
    }
    
    // Listar fila
    if ($action === 'queue-list') {
        $db = Database::getInstance();
        $sql = "SELECT * FROM email_queue ORDER BY created_at DESC LIMIT 20";
        $queue = $db->fetchAll($sql);
        
        echo json_encode([
            'success' => true,
            'queue' => $queue,
            'count' => count($queue)
        ]);
        exit;
    }
    
    // Processar fila
    if ($action === 'process-queue') {
        $email = new Email(false);
        $result = $email->processQueue(10);
        
        echo json_encode([
            'success' => true,
            'result' => $result
        ]);
        exit;
    }
    
    // Enviar email
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $toEmail = $data['to_email'] ?? '';
        $toName = $data['to_name'] ?? '';
        $template = $data['template'] ?? 'welcome';
        $useQueue = $data['use_queue'] ?? true;
        
        if (empty($toEmail) || empty($toName)) {
            throw new Exception('Email e nome são obrigatórios');
        }
        
        $email = new Email($useQueue);
        $result = false;
        
        switch ($template) {
            case 'welcome':
                $result = $email->sendWelcome($toEmail, $toName, 'Starter');
                break;
            case 'verify-email':
                $result = $email->sendVerification($toEmail, $toName, 'TOKEN_' . bin2hex(random_bytes(16)));
                break;
            case 'reset-password':
                $result = $email->sendPasswordReset($toEmail, $toName, 'RESET_' . bin2hex(random_bytes(16)));
                break;
            case 'payment-confirmed':
                $result = $email->sendPaymentConfirmed($toEmail, $toName, 'Starter', 20.00, 'TXN_TEST_123');
                break;
            case 'subscription-expiring':
                $result = $email->sendSubscriptionExpiring($toEmail, $toName, 'Starter', 7, date('Y-m-d', strtotime('+7 days')));
                break;
            default:
                throw new Exception('Template inválido');
        }
        
        echo json_encode([
            'success' => $result,
            'message' => $result 
                ? ($useQueue ? 'Email adicionado à fila com sucesso!' : 'Email enviado com sucesso!') 
                : 'Falha ao enviar email',
            'use_queue' => $useQueue
        ]);
        exit;
    }
    
    throw new Exception('Método não suportado');
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
