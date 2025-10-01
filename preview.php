<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';
require_once 'lib/QRCodeGenerator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Dados inválidos');
    }
    
    $qrType = sanitizeInput($input['qr_type'] ?? 'text');
    $size = (int)($input['size'] ?? DEFAULT_SIZE);
    $errorCorrection = sanitizeInput($input['error_correction'] ?? DEFAULT_ERROR_CORRECTION);
    
    // Validar tamanho
    if ($size < MIN_SIZE || $size > MAX_SIZE || !in_array($size, [200, 300, 400, 500])) {
        $size = DEFAULT_SIZE;
    }
    
    // Validar nível de correção de erro
    if (!in_array($errorCorrection, ['L', 'M', 'Q', 'H'])) {
        $errorCorrection = DEFAULT_ERROR_CORRECTION;
    }
    
    // Sanitizar dados de entrada
    $sanitizedData = [];
    foreach ($input as $key => $value) {
        if (is_string($value)) {
            $sanitizedData[$key] = sanitizeInput($value);
        } else {
            $sanitizedData[$key] = $value;
        }
    }
    
    // Criar instância do gerador
    $qrGenerator = new QRCodeGenerator($size, $errorCorrection);
    
    // Validar dados básicos
    if (!$qrGenerator->validateData($qrType, $sanitizedData)) {
        throw new Exception('Dados inválidos para o tipo de QR Code selecionado');
    }
    
    // Formatar dados
    $qrData = $qrGenerator->formatData($qrType, $sanitizedData);
    
    // Verificar se os dados não estão vazios
    if (empty(trim($qrData))) {
        throw new Exception('Os dados não podem estar vazios');
    }
    
    // Verificar tamanho dos dados
    if (strlen($qrData) > MAX_DATA_LENGTH) {
        throw new Exception('Os dados são muito longos para gerar um QR Code');
    }
    
    // Gerar URL do QR Code para preview
    $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
    $params = http_build_query([
        'size' => $size . 'x' . $size,
        'ecc' => $errorCorrection,
        'data' => $qrData
    ]);
    
    $qrUrl = $apiUrl . '?' . $params;
    
    // Obter informações do QR Code
    $qrInfo = $qrGenerator->getQRInfo($qrType, $sanitizedData);
    
    // Retornar resposta
    echo json_encode([
        'success' => true,
        'qr_url' => $qrUrl,
        'qr_info' => $qrInfo,
        'data_length' => strlen($qrData),
        'data_preview' => substr($qrData, 0, 100) . (strlen($qrData) > 100 ? '...' : '')
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}