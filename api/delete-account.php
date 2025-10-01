<?php
require_once __DIR__ . '/../includes/database.php';

$auth = new Auth();
$auth->requireLogin();

$db = Database::getInstance();
$userId = $auth->getUserId();

// Excluir todos os dados do usuário
$db->query("DELETE FROM microsites WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM qrcodes WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM analytics WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM notifications WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM subscriptions WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM payments WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM settings WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM sessions WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM audit_logs WHERE user_id = ?", [$userId]);
$db->query("DELETE FROM users WHERE id = ?", [$userId]);

// Fazer logout
session_destroy();

// Redirecionar para home
header('Location: ../home.php?deleted=1');
exit;
