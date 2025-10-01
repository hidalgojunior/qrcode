<?php
session_start();
require_once 'config.php';
require_once 'lib/QRCodeGenerator.php';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método de requisição inválido.';
    header('Location: index.php');
    exit;
}

// Verificar rate limiting
if (!checkRateLimit()) {
    $_SESSION['error'] = 'Limite de requisições excedido. Tente novamente mais tarde.';
    logActivity("Rate limit excedido para IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 'WARNING');
    header('Location: index.php');
    exit;
}

try {
    // Obter dados do formulário
    $qrType = sanitizeInput($_POST['qr_type'] ?? 'text');
    $size = (int)($_POST['size'] ?? DEFAULT_SIZE);
    $errorCorrection = sanitizeInput($_POST['error_correction'] ?? DEFAULT_ERROR_CORRECTION);
    
    // Obter configurações de personalização (se enviadas via JavaScript)
    $bgColor = sanitizeInput($_POST['bg_color'] ?? 'ffffff');
    $fgColor = sanitizeInput($_POST['fg_color'] ?? '000000');
    $margin = (int)($_POST['margin'] ?? 1);
    
    // Limpar cores (remover #)
    $bgColor = ltrim($bgColor, '#');
    $fgColor = ltrim($fgColor, '#');
    
    // Validar cores hexadecimais
    if (!preg_match('/^[0-9A-Fa-f]{6}$/', $bgColor)) {
        $bgColor = 'ffffff';
    }
    if (!preg_match('/^[0-9A-Fa-f]{6}$/', $fgColor)) {
        $fgColor = '000000';
    }
    
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
    foreach ($_POST as $key => $value) {
        if (is_string($value)) {
            $sanitizedData[$key] = sanitizeInput($value);
        } else {
            $sanitizedData[$key] = $value;
        }
    }
    
    // Criar instância do gerador
    $qrGenerator = new QRCodeGenerator($size, $errorCorrection);
    
    // Validar dados
    if (!$qrGenerator->validateData($qrType, $sanitizedData)) {
        $_SESSION['error'] = 'Dados inválidos para o tipo de QR Code selecionado.';
        logActivity("Validação falhou para tipo: {$qrType}", 'WARNING');
        header('Location: index.php');
        exit;
    }
    
    // Formatar dados
    $qrData = $qrGenerator->formatData($qrType, $sanitizedData);
    
    // Verificar se os dados não estão vazios
    if (empty(trim($qrData))) {
        $_SESSION['error'] = 'Os dados para gerar o QR Code não podem estar vazios.';
        header('Location: index.php');
        exit;
    }
    
    // Verificar tamanho dos dados
    if (strlen($qrData) > MAX_DATA_LENGTH) {
        $_SESSION['error'] = 'Os dados são muito longos para gerar um QR Code.';
        logActivity("Dados muito longos: " . strlen($qrData) . " caracteres", 'WARNING');
        header('Location: index.php');
        exit;
    }
    
    // Gerar QR Code
    $result = $qrGenerator->generateQRCode($qrData, null, $bgColor, $fgColor, $margin);
    
    // Obter informações do QR Code
    $qrInfo = $qrGenerator->getQRInfo($qrType, $sanitizedData);
    
    // Limpar arquivos antigos se habilitado
    if (AUTO_CLEANUP_ENABLED) {
        $qrGenerator->cleanOldFiles();
    }
    
    // Log da atividade
    logActivity("QR Code gerado: {$qrType}, tamanho: {$size}, arquivo: {$result['filename']}", 'INFO');
    
    // Armazenar informações na sessão
    $_SESSION['qr_result'] = $result;
    $_SESSION['qr_info'] = $qrInfo;
    $_SESSION['qr_generated'] = true;
    
    // Redirecionar para a página de resultado
    header('Location: result.php');
    exit;
    
} catch (Exception $e) {
    $errorMsg = 'Erro ao gerar QR Code: ' . $e->getMessage();
    $_SESSION['error'] = $errorMsg;
    logActivity($errorMsg, 'ERROR');
    header('Location: index.php');
    exit;
}