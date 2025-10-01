<?php
/**
 * Webhook de Confirmação de Pagamento PIX
 * Recebe notificações do gateway de pagamento
 */

require_once __DIR__ . '/../includes/database.php';

// Configurações de segurança
define('WEBHOOK_SECRET', getenv('WEBHOOK_SECRET') ?: 'devmenthors_webhook_secret_key_2025');
define('ALLOWED_IPS', [
    '127.0.0.1', // Local (para testes)
    '::1',       // Local IPv6
    // Adicionar IPs dos gateways em produção:
    // '177.12.34.56', // Mercado Pago
    // '187.45.67.89', // PagSeguro
    // '179.23.45.67', // Asaas
]);

// Headers de segurança
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Log de requisições
$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
    'headers' => getallheaders(),
    'body' => file_get_contents('php://input'),
];

// Registrar log
file_put_contents(
    __DIR__ . '/../logs/webhook-' . date('Y-m-d') . '.log',
    json_encode($logData, JSON_PRETTY_PRINT) . "\n\n",
    FILE_APPEND
);

/**
 * Valida o IP de origem
 */
function validateIP() {
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Em desenvolvimento, permitir localhost
    if (getenv('APP_ENV') === 'development') {
        return true;
    }
    
    return in_array($clientIP, ALLOWED_IPS);
}

/**
 * Valida a assinatura do webhook
 */
function validateSignature($payload, $signature) {
    $expectedSignature = hash_hmac('sha256', $payload, WEBHOOK_SECRET);
    return hash_equals($expectedSignature, $signature);
}

/**
 * Processa pagamento confirmado
 */
function processPayment($data) {
    $db = Database::getInstance();
    
    try {
        // Extrair dados do pagamento
        $paymentId = $data['payment_id'] ?? null;
        $status = $data['status'] ?? null;
        $amount = $data['amount'] ?? null;
        $transactionId = $data['transaction_id'] ?? null;
        
        if (!$paymentId) {
            throw new Exception('Payment ID não fornecido');
        }
        
        // Buscar pagamento no banco
        $sql = "SELECT p.*, u.email, u.name, pl.name as plan_name 
                FROM payments p
                JOIN users u ON p.user_id = u.id
                JOIN plans pl ON p.plan_id = pl.id
                WHERE p.id = ?";
        $payment = $db->fetch($sql, [$paymentId]);
        
        if (!$payment) {
            throw new Exception('Pagamento não encontrado');
        }
        
        // Verificar se já foi processado
        if ($payment['status'] === 'paid') {
            return [
                'success' => true,
                'message' => 'Pagamento já processado anteriormente',
                'already_processed' => true
            ];
        }
        
        // Iniciar transação
        $db->beginTransaction();
        
        // 1. Atualizar status do pagamento
        $sql = "UPDATE payments 
                SET status = 'paid', 
                    transaction_id = ?,
                    paid_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?";
        $db->query($sql, [$transactionId, $paymentId]);
        
        // 2. Verificar se já existe assinatura ativa
        $sql = "SELECT * FROM subscriptions 
                WHERE user_id = ? AND status = 'active'
                ORDER BY end_date DESC LIMIT 1";
        $existingSubscription = $db->fetch($sql, [$payment['user_id']]);
        
        if ($existingSubscription) {
            // Estender assinatura existente
            $sql = "UPDATE subscriptions 
                    SET end_date = DATE_ADD(end_date, INTERVAL 1 MONTH),
                        updated_at = NOW()
                    WHERE id = ?";
            $db->query($sql, [$existingSubscription['id']]);
            $subscriptionId = $existingSubscription['id'];
        } else {
            // Criar nova assinatura
            $sql = "INSERT INTO subscriptions (user_id, plan_id, status, start_date, end_date) 
                    VALUES (?, ?, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH))";
            $db->query($sql, [$payment['user_id'], $payment['plan_id']]);
            $subscriptionId = $db->lastInsertId();
        }
        
        // 3. Atualizar plano do usuário
        $sql = "UPDATE users SET plan_id = ?, updated_at = NOW() WHERE id = ?";
        $db->query($sql, [$payment['plan_id'], $payment['user_id']]);
        
        // 4. Criar notificação para o usuário
        $message = sprintf(
            "Seu pagamento de R$ %.2f para o plano %s foi confirmado! Sua assinatura está ativa.",
            $payment['amount'],
            $payment['plan_name']
        );
        
        $sql = "INSERT INTO notifications (user_id, type, title, message, created_at) 
                VALUES (?, 'payment', 'Pagamento Confirmado! 🎉', ?, NOW())";
        $db->query($sql, [$payment['user_id'], $message]);
        
        // 5. Registrar em audit_logs
        $auditData = [
            'payment_id' => $paymentId,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'plan_id' => $payment['plan_id'],
            'subscription_id' => $subscriptionId
        ];
        
        $sql = "INSERT INTO audit_logs (user_id, action, description, ip_address, user_agent, created_at) 
                VALUES (?, 'payment_confirmed', ?, ?, ?, NOW())";
        $db->query($sql, [
            $payment['user_id'],
            json_encode($auditData),
            $_SERVER['REMOTE_ADDR'] ?? 'webhook',
            $_SERVER['HTTP_USER_AGENT'] ?? 'webhook'
        ]);
        
        // Commit da transação
        $db->commit();
        
        // TODO: Enviar email de confirmação
        // sendPaymentConfirmationEmail($payment['email'], $payment['name'], $payment['plan_name'], $payment['amount']);
        
        return [
            'success' => true,
            'message' => 'Pagamento processado com sucesso',
            'subscription_id' => $subscriptionId,
            'user_id' => $payment['user_id'],
            'plan_id' => $payment['plan_id']
        ];
        
    } catch (Exception $e) {
        $db->rollback();
        
        // Log do erro
        error_log("Erro ao processar pagamento: " . $e->getMessage());
        
        throw $e;
    }
}

/**
 * Processa pagamento expirado/cancelado
 */
function processPaymentFailure($data) {
    $db = Database::getInstance();
    
    try {
        $paymentId = $data['payment_id'] ?? null;
        $status = $data['status'] ?? 'expired';
        
        if (!$paymentId) {
            throw new Exception('Payment ID não fornecido');
        }
        
        // Atualizar status do pagamento
        $sql = "UPDATE payments 
                SET status = ?, updated_at = NOW()
                WHERE id = ?";
        $db->query($sql, [$status, $paymentId]);
        
        // Buscar dados do usuário
        $sql = "SELECT u.id, u.name FROM payments p
                JOIN users u ON p.user_id = u.id
                WHERE p.id = ?";
        $payment = $db->fetch($sql, [$paymentId]);
        
        if ($payment) {
            // Criar notificação
            $message = $status === 'expired' 
                ? "O prazo de pagamento expirou. Por favor, gere um novo código PIX."
                : "O pagamento foi cancelado.";
            
            $sql = "INSERT INTO notifications (user_id, type, title, message, created_at) 
                    VALUES (?, 'payment', 'Pagamento não processado', ?, NOW())";
            $db->query($sql, [$payment['id'], $message]);
        }
        
        return [
            'success' => true,
            'message' => 'Status atualizado para ' . $status
        ];
        
    } catch (Exception $e) {
        error_log("Erro ao processar falha de pagamento: " . $e->getMessage());
        throw $e;
    }
}

// ==================================================
// MAIN: Processar requisição
// ==================================================

try {
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Método não permitido. Use POST.'
        ]);
        exit;
    }
    
    // Validar IP de origem
    if (!validateIP()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'IP não autorizado'
        ]);
        exit;
    }
    
    // Ler payload
    $payload = file_get_contents('php://input');
    $data = json_decode($payload, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'JSON inválido'
        ]);
        exit;
    }
    
    // Validar assinatura (se fornecida)
    $signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? null;
    if ($signature && !validateSignature($payload, $signature)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Assinatura inválida'
        ]);
        exit;
    }
    
    // Processar baseado no status
    $status = $data['status'] ?? null;
    
    switch ($status) {
        case 'paid':
        case 'approved':
        case 'confirmed':
            $result = processPayment($data);
            break;
            
        case 'expired':
        case 'cancelled':
        case 'failed':
            $result = processPaymentFailure($data);
            break;
            
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Status desconhecido: ' . $status
            ]);
            exit;
    }
    
    // Resposta de sucesso
    http_response_code(200);
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
