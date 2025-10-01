<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $auth->getUserId();

$qrcodeId = $_GET['id'] ?? null;

if (!$qrcodeId) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

// Buscar QR code
$qrcode = $db->fetch(
    "SELECT * FROM qrcodes WHERE id = ? AND user_id = ?",
    [$qrcodeId, $userId]
);

if (!$qrcode) {
    echo json_encode(['success' => false, 'message' => 'QR Code não encontrado']);
    exit;
}

echo json_encode(['success' => true, 'qrcode' => $qrcode]);
