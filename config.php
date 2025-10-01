<?php
/**
 * Configurações do sistema de QR Code
 */

// Configurações do aplicativo
define('APP_NAME', 'Gerador de QR Code');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Seu Nome');

// Configurações de diretórios
define('QR_CODES_DIR', __DIR__ . '/qrcodes/');
define('ASSETS_DIR', __DIR__ . '/assets/');
define('MICROSITE_DIR', __DIR__ . '/microsites/');
define('TMP_DIR', __DIR__ . '/tmp/');

// Configurações de QR Code
define('DEFAULT_SIZE', 300);
define('MIN_SIZE', 100);
define('MAX_SIZE', 1000);
define('DEFAULT_ERROR_CORRECTION', 'M');

// Configurações de limpeza automática
define('AUTO_CLEANUP_ENABLED', true);
define('MAX_FILE_AGE_HOURS', 24);

// Configurações de segurança
define('MAX_DATA_LENGTH', 4000);
define('ALLOWED_FILE_TYPES', ['png', 'jpg', 'jpeg']);

// Configurações de rate limiting (limite de requisições)
define('RATE_LIMIT_ENABLED', true);
define('MAX_REQUESTS_PER_HOUR', 100);

/**
 * Função para validar entrada
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Função para registrar logs
 */
function logActivity($message, $level = 'INFO') {
    $logFile = __DIR__ . '/logs/activity.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Função para verificar rate limiting
 */
function checkRateLimit() {
    if (!RATE_LIMIT_ENABLED) {
        return true;
    }
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateLimitFile = __DIR__ . '/tmp/rate_limit_' . md5($ip) . '.txt';
    $rateLimitDir = dirname($rateLimitFile);
    
    if (!is_dir($rateLimitDir)) {
        mkdir($rateLimitDir, 0755, true);
    }
    
    $now = time();
    $hourAgo = $now - 3600;
    
    // Ler tentativas existentes
    $attempts = [];
    if (file_exists($rateLimitFile)) {
        $content = file_get_contents($rateLimitFile);
        $attempts = $content ? explode(',', $content) : [];
    }
    
    // Filtrar tentativas da última hora
    $attempts = array_filter($attempts, function($timestamp) use ($hourAgo) {
        return $timestamp > $hourAgo;
    });
    
    // Verificar se excedeu o limite
    if (count($attempts) >= MAX_REQUESTS_PER_HOUR) {
        return false;
    }
    
    // Adicionar nova tentativa
    $attempts[] = $now;
    file_put_contents($rateLimitFile, implode(',', $attempts));
    
    return true;
}

/**
 * Função para limpar arquivos temporários
 */
function cleanupTempFiles() {
    $tempDir = __DIR__ . '/tmp/';
    if (!is_dir($tempDir)) {
        return;
    }
    
    $files = glob($tempDir . '*');
    $now = time();
    
    foreach ($files as $file) {
        if ($now - filemtime($file) > 3600) { // 1 hora
            unlink($file);
        }
    }
}