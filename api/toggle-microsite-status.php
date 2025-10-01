<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $auth->getUserId();

$input = json_decode(file_get_contents('php://input'), true);
$micrositeId = $input['id'] ?? null;
$newStatus = $input['status'] ?? null;

if (!$micrositeId || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Verificar se o microsite pertence ao usuário
$microsite = $db->fetch(
    "SELECT id FROM microsites WHERE id = ? AND user_id = ?",
    [$micrositeId, $userId]
);

if (!$microsite) {
    echo json_encode(['success' => false, 'message' => 'Microsite não encontrado']);
    exit;
}

// Atualizar status
$updated = $db->query(
    "UPDATE microsites SET status = ?, updated_at = NOW() WHERE id = ?",
    [$newStatus, $micrositeId]
);

if ($updated) {
    // Registrar auditoria
    $db->query(
        "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)",
        [$userId, 'microsite_status_change', "Status alterado para: $newStatus", $_SERVER['REMOTE_ADDR']]
    );
    
    echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
}
