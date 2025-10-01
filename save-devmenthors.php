<?php
require_once 'config.php';

header('Content-Type: application/json');

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Pegar dados JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['name']) || !isset($data['slug'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Validar formato do slug
$slug = $data['slug'];
if (!preg_match('/^[a-z0-9-]{3,50}$/', $slug)) {
    echo json_encode(['success' => false, 'message' => 'URL personalizada inválida. Use apenas letras minúsculas, números e hífen.']);
    exit;
}

// Criar diretório se não existir
if (!file_exists(MICROSITE_DIR)) {
    mkdir(MICROSITE_DIR, 0755, true);
}

// Verificar se slug já existe
$micrositeFile = MICROSITE_DIR . '/' . $slug . '.json';
if (file_exists($micrositeFile)) {
    echo json_encode(['success' => false, 'message' => 'Esta URL já está em uso. Por favor, escolha outra.']);
    exit;
}

// Salvar dados do microsite
if (file_put_contents($micrositeFile, json_encode($data, JSON_PRETTY_PRINT))) {
    echo json_encode([
        'success' => true,
        'id' => $slug,
        'url' => 'devmenthors.php?id=' . $slug
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar microsite']);
}
