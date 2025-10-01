<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $auth->getUserId();

// Limpar notificações lidas
$deleted = $db->query(
    "DELETE FROM notifications WHERE user_id = ? AND is_read = 1",
    [$userId]
);

echo json_encode(['success' => $deleted]);
