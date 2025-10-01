<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $auth->getUserId();

// Buscar todos os dados do usuário
$user = $auth->getUser();

$data = [
    'user' => [
        'name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'created_at' => $user['created_at']
    ],
    'microsites' => $db->fetchAll(
        "SELECT slug, name, data, views, status, created_at FROM microsites WHERE user_id = ?",
        [$userId]
    ),
    'qrcodes' => $db->fetchAll(
        "SELECT type, content, size, format, downloads, created_at FROM qrcodes WHERE user_id = ?",
        [$userId]
    ),
    'subscriptions' => $db->fetchAll(
        "SELECT s.*, p.name as plan_name FROM subscriptions s 
         JOIN plans p ON s.plan_id = p.id 
         WHERE s.user_id = ?",
        [$userId]
    ),
    'analytics' => $db->fetchAll(
        "SELECT * FROM analytics WHERE user_id = ?",
        [$userId]
    )
];

// Gerar arquivo JSON
$filename = "devmenthors-export-" . date('Y-m-d') . ".json";
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
