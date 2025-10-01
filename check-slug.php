<?php
require_once 'config.php';

header('Content-Type: application/json');

$slug = $_GET['slug'] ?? '';

// Validar formato do slug
if (!preg_match('/^[a-z0-9-]{3,50}$/', $slug)) {
    echo json_encode(['available' => false, 'error' => 'Formato inválido']);
    exit;
}

// Verificar se já existe
$micrositeFile = MICROSITE_DIR . '/' . $slug . '.json';

if (file_exists($micrositeFile)) {
    echo json_encode(['available' => false]);
} else {
    echo json_encode(['available' => true]);
}
