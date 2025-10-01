<?php
/**
 * API para listar pagamentos (para teste)
 */

require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::getInstance();
    
    // Buscar últimos 10 pagamentos
    $sql = "SELECT p.id, p.user_id, p.plan_id, p.amount, p.status, 
                   p.pix_code, p.expires_at, p.created_at,
                   pl.name as plan_name, u.name as user_name, u.email
            FROM payments p
            JOIN plans pl ON p.plan_id = pl.id
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
            LIMIT 10";
    
    $payments = $db->fetchAll($sql);
    
    echo json_encode([
        'success' => true,
        'payments' => $payments,
        'count' => count($payments)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
