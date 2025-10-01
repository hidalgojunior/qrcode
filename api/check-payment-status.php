<?php
/**
 * API para verificar status de pagamento
 * Permite polling manual do status
 */

require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar autenticação
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Não autenticado'
    ]);
    exit;
}

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método não permitido'
    ]);
    exit;
}

try {
    $paymentId = $_GET['payment_id'] ?? null;
    
    if (!$paymentId) {
        throw new Exception('Payment ID é obrigatório');
    }
    
    $db = Database::getInstance();
    $userId = $auth->getUserId();
    
    // Buscar pagamento
    $sql = "SELECT p.*, pl.name as plan_name, pl.price
            FROM payments p
            JOIN plans pl ON p.plan_id = pl.id
            WHERE p.id = ? AND p.user_id = ?";
    $payment = $db->fetch($sql, [$paymentId, $userId]);
    
    if (!$payment) {
        throw new Exception('Pagamento não encontrado');
    }
    
    // Verificar se expirou
    $expiresAt = strtotime($payment['expires_at']);
    $isExpired = time() > $expiresAt;
    
    // Se expirou e ainda está pendente, atualizar status
    if ($isExpired && $payment['status'] === 'pending') {
        $sql = "UPDATE payments SET status = 'expired', updated_at = NOW() WHERE id = ?";
        $db->query($sql, [$paymentId]);
        $payment['status'] = 'expired';
    }
    
    // Se foi pago, buscar assinatura
    $subscription = null;
    if ($payment['status'] === 'paid') {
        $sql = "SELECT * FROM subscriptions 
                WHERE user_id = ? AND plan_id = ?
                ORDER BY created_at DESC LIMIT 1";
        $subscription = $db->fetch($sql, [$userId, $payment['plan_id']]);
    }
    
    // Montar resposta
    $response = [
        'success' => true,
        'payment' => [
            'id' => $payment['id'],
            'status' => $payment['status'],
            'amount' => floatval($payment['amount']),
            'plan_name' => $payment['plan_name'],
            'created_at' => $payment['created_at'],
            'expires_at' => $payment['expires_at'],
            'paid_at' => $payment['paid_at'],
            'is_expired' => $isExpired,
            'transaction_id' => $payment['transaction_id']
        ]
    ];
    
    // Adicionar dados da assinatura se existir
    if ($subscription) {
        $response['subscription'] = [
            'id' => $subscription['id'],
            'status' => $subscription['status'],
            'start_date' => $subscription['start_date'],
            'end_date' => $subscription['end_date']
        ];
    }
    
    // Adicionar flag de ações disponíveis
    $response['actions'] = [
        'can_retry' => in_array($payment['status'], ['expired', 'cancelled']),
        'can_cancel' => $payment['status'] === 'pending' && !$isExpired,
        'is_active' => $payment['status'] === 'paid'
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
