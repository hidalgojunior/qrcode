<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $auth->getUserId();

// Marcar todas como lidas
$updated = $db->query(
    "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
    [$userId]
);

echo json_encode(['success' => $updated]);
