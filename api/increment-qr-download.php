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

// Incrementar contador de downloads
$updated = $db->query(
    "UPDATE qrcodes SET downloads = downloads + 1 WHERE id = ?",
    [$qrcodeId]
);

echo json_encode(['success' => $updated]);
