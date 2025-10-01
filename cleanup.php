<?php
/**
 * Script de limpeza automática
 * Execute este script via cron para limpeza automática de arquivos antigos
 * 
 * Exemplo de configuração cron (executar a cada hora):
 * 0 * * * * /usr/bin/php /caminho/para/cleanup.php
 */

require_once __DIR__ . '/config.php';

// Função principal de limpeza
function runCleanup() {
    $cleaned = 0;
    $errors = [];
    
    try {
        // Limpar QR codes antigos
        $qrCleaned = cleanupQRCodes();
        $cleaned += $qrCleaned;
        
        // Limpar arquivos temporários
        $tempCleaned = cleanupTempFiles();
        $cleaned += $tempCleaned;
        
        // Limpar logs antigos
        $logsCleaned = cleanupOldLogs();
        $cleaned += $logsCleaned;
        
        logActivity("Limpeza automática concluída. {$cleaned} arquivos removidos.", 'INFO');
        
    } catch (Exception $e) {
        $error = "Erro durante limpeza automática: " . $e->getMessage();
        $errors[] = $error;
        logActivity($error, 'ERROR');
    }
    
    return [
        'cleaned' => $cleaned,
        'errors' => $errors
    ];
}

// Função para limpar QR codes antigos
function cleanupQRCodes() {
    $qrDir = QR_CODES_DIR;
    $maxAge = MAX_FILE_AGE_HOURS * 3600; // converter para segundos
    $cleaned = 0;
    
    if (!is_dir($qrDir)) {
        return 0;
    }
    
    $files = glob($qrDir . '*.{png,jpg,jpeg}', GLOB_BRACE);
    $now = time();
    
    foreach ($files as $file) {
        if ($now - filemtime($file) > $maxAge) {
            if (unlink($file)) {
                $cleaned++;
            }
        }
    }
    
    return $cleaned;
}

// Função para limpar logs antigos (manter apenas últimos 30 dias)
function cleanupOldLogs() {
    $logsDir = __DIR__ . '/logs/';
    $maxAge = 30 * 24 * 3600; // 30 dias em segundos
    $cleaned = 0;
    
    if (!is_dir($logsDir)) {
        return 0;
    }
    
    $files = glob($logsDir . '*.log');
    $now = time();
    
    foreach ($files as $file) {
        if ($now - filemtime($file) > $maxAge) {
            if (unlink($file)) {
                $cleaned++;
            }
        }
    }
    
    return $cleaned;
}

// Função para otimizar logs (remover entradas antigas do arquivo ativo)
function optimizeActiveLogs() {
    $logFile = __DIR__ . '/logs/activity.log';
    
    if (!file_exists($logFile)) {
        return;
    }
    
    $lines = file($logFile);
    $maxAge = 7 * 24 * 3600; // 7 dias
    $now = time();
    $newLines = [];
    
    foreach ($lines as $line) {
        // Extrair timestamp da linha de log
        if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
            $logTime = strtotime($matches[1]);
            if ($now - $logTime <= $maxAge) {
                $newLines[] = $line;
            }
        }
    }
    
    // Reescrever arquivo apenas com entradas recentes
    if (count($newLines) < count($lines)) {
        file_put_contents($logFile, implode('', $newLines));
        logActivity("Log otimizado. " . (count($lines) - count($newLines)) . " entradas antigas removidas.", 'INFO');
    }
}

// Função para gerar relatório de uso de espaço
function generateSpaceReport() {
    $report = [];
    
    // Verificar espaço usado por QR codes
    $qrSize = 0;
    $qrCount = 0;
    $qrFiles = glob(QR_CODES_DIR . '*.{png,jpg,jpeg}', GLOB_BRACE);
    
    foreach ($qrFiles as $file) {
        $qrSize += filesize($file);
        $qrCount++;
    }
    
    // Verificar espaço usado por logs
    $logSize = 0;
    $logCount = 0;
    $logFiles = glob(__DIR__ . '/logs/*.log');
    
    foreach ($logFiles as $file) {
        $logSize += filesize($file);
        $logCount++;
    }
    
    // Verificar espaço usado por arquivos temporários
    $tempSize = 0;
    $tempCount = 0;
    if (is_dir(__DIR__ . '/tmp/')) {
        $tempFiles = glob(__DIR__ . '/tmp/*');
        foreach ($tempFiles as $file) {
            if (is_file($file)) {
                $tempSize += filesize($file);
                $tempCount++;
            }
        }
    }
    
    $report = [
        'qr_codes' => [
            'count' => $qrCount,
            'size' => $qrSize,
            'size_mb' => round($qrSize / 1024 / 1024, 2)
        ],
        'logs' => [
            'count' => $logCount,
            'size' => $logSize,
            'size_mb' => round($logSize / 1024 / 1024, 2)
        ],
        'temp' => [
            'count' => $tempCount,
            'size' => $tempSize,
            'size_mb' => round($tempSize / 1024 / 1024, 2)
        ],
        'total_size_mb' => round(($qrSize + $logSize + $tempSize) / 1024 / 1024, 2)
    ];
    
    return $report;
}

// Executar limpeza se chamado via linha de comando
if (php_sapi_name() === 'cli') {
    echo "Iniciando limpeza automática...\n";
    
    $result = runCleanup();
    
    echo "Limpeza concluída!\n";
    echo "Arquivos removidos: " . $result['cleaned'] . "\n";
    
    if (!empty($result['errors'])) {
        echo "Erros encontrados:\n";
        foreach ($result['errors'] as $error) {
            echo "- " . $error . "\n";
        }
    }
    
    // Otimizar logs
    optimizeActiveLogs();
    
    // Gerar relatório de espaço
    $report = generateSpaceReport();
    echo "\nRelatório de uso de espaço:\n";
    echo "QR Codes: " . $report['qr_codes']['count'] . " arquivos (" . $report['qr_codes']['size_mb'] . " MB)\n";
    echo "Logs: " . $report['logs']['count'] . " arquivos (" . $report['logs']['size_mb'] . " MB)\n";
    echo "Temporários: " . $report['temp']['count'] . " arquivos (" . $report['temp']['size_mb'] . " MB)\n";
    echo "Total: " . $report['total_size_mb'] . " MB\n";
    
} else {
    // Se executado via web, retornar JSON
    header('Content-Type: application/json');
    
    // Verificar se é uma requisição autorizada (adicione autenticação se necessário)
    $result = runCleanup();
    optimizeActiveLogs();
    $report = generateSpaceReport();
    
    echo json_encode([
        'success' => true,
        'cleaned' => $result['cleaned'],
        'errors' => $result['errors'],
        'space_report' => $report,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}