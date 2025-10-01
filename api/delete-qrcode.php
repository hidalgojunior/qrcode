<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $auth->getUserId();

$input = json_decode(file_get_contents('php://input'), true);
$qrcodeId = $input['id'] ?? null;

if (!$qrcodeId) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

// Verificar se o QR code pertence ao usuário
$qrcode = $db->fetch(
    "SELECT id FROM qrcodes WHERE id = ? AND user_id = ?",
    [$qrcodeId, $userId]
);

if (!$qrcode) {
    echo json_encode(['success' => false, 'message' => 'QR Code não encontrado']);
    exit;
}

// Excluir QR code
$deleted = $db->query("DELETE FROM qrcodes WHERE id = ?", [$qrcodeId]);

if ($deleted) {
    // Registrar auditoria
    $db->query(
        "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)",
        [$userId, 'qrcode_delete', "QR Code excluído: ID $qrcodeId", $_SERVER['REMOTE_ADDR']]
    );
    
    echo json_encode(['success' => true, 'message' => 'QR Code excluído com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir QR Code']);
}
