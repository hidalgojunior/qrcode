<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $auth->getUserId();

$input = json_decode(file_get_contents('php://input'), true);
$micrositeId = $input['id'] ?? null;

if (!$micrositeId) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

// Verificar se o microsite pertence ao usuário
$microsite = $db->fetch(
    "SELECT id, name FROM microsites WHERE id = ? AND user_id = ?",
    [$micrositeId, $userId]
);

if (!$microsite) {
    echo json_encode(['success' => false, 'message' => 'Microsite não encontrado']);
    exit;
}

// Excluir microsite
$deleted = $db->query("DELETE FROM microsites WHERE id = ?", [$micrositeId]);

if ($deleted) {
    // Registrar auditoria
    $db->query(
        "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)",
        [$userId, 'microsite_delete', "Microsite excluído: {$microsite['name']}", $_SERVER['REMOTE_ADDR']]
    );
    
    echo json_encode(['success' => true, 'message' => 'Microsite excluído com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir microsite']);
}
