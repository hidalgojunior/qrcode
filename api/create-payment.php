<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Método não permitido', 405);
}

try {
    $auth = new Auth();
    $auth->requireLogin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    $planId = $data['plan_id'] ?? null;
    
    if (!$planId) {
        error_response('ID do plano é obrigatório');
    }
    
    $db = Database::getInstance();
    $userId = $auth->getUserId();
    
    // Buscar plano
    $plan = $db->fetch("SELECT * FROM plans WHERE id = ?", [$planId]);
    
    if (!$plan) {
        error_response('Plano não encontrado');
    }
    
    // Criar assinatura pendente
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime('+1 month'));
    
    $db->query(
        "INSERT INTO subscriptions (user_id, plan_id, status, start_date, end_date, payment_method)
         VALUES (?, ?, 'pending', ?, ?, 'pix')",
        [$userId, $planId, $startDate, $endDate]
    );
    
    $subscriptionId = $db->lastInsertId();
    
    // Gerar código PIX (simulado - em produção, integrar com gateway de pagamento)
    $pixCode = generatePixCode($plan['price'], $userId, $subscriptionId);
    $pixTxid = 'TXN' . time() . rand(1000, 9999);
    
    // Criar registro de pagamento
    $db->query(
        "INSERT INTO payments (user_id, subscription_id, amount, payment_method, status, pix_code, pix_txid, expires_at)
         VALUES (?, ?, ?, 'pix', 'pending', ?, ?, ?)",
        [
            $userId,
            $subscriptionId,
            $plan['price'],
            $pixCode,
            $pixTxid,
            date('Y-m-d H:i:s', time() + 1800) // Expira em 30 minutos
        ]
    );
    
    $paymentId = $db->lastInsertId();
    
    // Gerar QR Code do PIX
    $qrcodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($pixCode);
    
    success_response([
        'payment_id' => $paymentId,
        'subscription_id' => $subscriptionId,
        'pix_code' => $pixCode,
        'pix_txid' => $pixTxid,
        'qrcode_url' => $qrcodeUrl,
        'amount' => $plan['price'],
        'expires_at' => date('d/m/Y H:i', time() + 1800)
    ], 'Pagamento criado com sucesso');
    
} catch (Exception $e) {
    error_response($e->getMessage());
}

/**
 * Gera código PIX (simulado)
 * Em produção, integrar com:
 * - Mercado Pago
 * - PagSeguro
 * - Asaas
 * - Outro gateway
 */
function generatePixCode($amount, $userId, $subscriptionId) {
    // Formato simplificado do PIX Copia e Cola
    // Em produção, usar biblioteca oficial do Banco Central
    
    $pixKey = "devmenthors@example.com"; // Chave PIX da empresa
    $merchantName = "DevMenthors";
    $merchantCity = "SAO PAULO";
    $txid = "SUBS" . str_pad($subscriptionId, 10, '0', STR_PAD_LEFT);
    
    // Isso é uma simulação. Use biblioteca real em produção!
    $pixCode = "00020126580014br.gov.bcb.pix0136" . $pixKey . 
               "52040000530398654" . sprintf("%02d%s", strlen($amount), $amount) . 
               "5802BR5913" . $merchantName . 
               "6009" . $merchantCity . 
               "62070503***" . $txid . "6304****";
    
    return $pixCode;
}
