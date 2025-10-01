<?php
/**
 * Script CRON para buscar emails automaticamente
 * Executa a cada 5 minutos para buscar novos emails
 * 
 * Cron: */5 * * * * php /caminho/para/process-inbox.php
 * Windows Task Scheduler: schtasks /create /sc minute /mo 5 /tn "InboxCheck" /tr "php c:\laragon\www\QrCode\process-inbox.php"
 */

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/inbox.php';
require_once __DIR__ . '/vendor/autoload.php';

// Verificar autenticação (CLI ou secret)
if (php_sapi_name() !== 'cli') {
    $secret = $_GET['secret'] ?? '';
    $expectedSecret = getenv('CRON_SECRET') ?: 'cron_secret_2025';
    
    if ($secret !== $expectedSecret) {
        http_response_code(403);
        die(json_encode(['error' => 'Acesso negado']));
    }
}

// Log file
$logFile = __DIR__ . '/logs/inbox-' . date('Y-m-d') . '.log';
$logDir = dirname($logFile);

if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

function log_message($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $line = "[$timestamp] $message\n";
    file_put_contents($logFile, $line, FILE_APPEND);
    echo $line;
}

try {
    log_message("=== Iniciando busca de emails ===");
    
    $inbox = new InboxManager();
    
    // Testar conexão primeiro
    log_message("Testando conexão IMAP...");
    $testResult = $inbox->testConnection();
    
    if (!$testResult['success']) {
        log_message("ERRO: Falha na conexão IMAP - " . $testResult['error']);
        exit(1);
    }
    
    log_message("Conexão OK - " . $testResult['info']['messages'] . " mensagens no servidor");
    log_message("Não lidas: " . $testResult['info']['unread']);
    
    // Buscar novos emails
    log_message("Buscando emails novos...");
    $result = $inbox->fetchNewEmails(50);
    
    if ($result['success']) {
        log_message("Processados: {$result['processed']} de {$result['total']} emails");
        
        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                log_message("AVISO: $error");
            }
        }
    } else {
        log_message("ERRO: " . $result['error']);
        exit(1);
    }
    
    // Estatísticas
    $stats = $inbox->getStats();
    log_message("Estatísticas:");
    log_message("- Total de emails: " . $stats['total']);
    log_message("- Não lidos: " . $stats['unread']);
    
    if (isset($stats['by_category'])) {
        log_message("Por categoria:");
        foreach ($stats['by_category'] as $category => $count) {
            log_message("  - {$category}: {$count}");
        }
    }
    
    log_message("=== Busca concluída com sucesso ===\n");
    
    // Se chamado via web, retornar JSON
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'result' => $result,
            'stats' => $stats
        ]);
    }
    
    exit(0);
    
} catch (Exception $e) {
    log_message("ERRO FATAL: " . $e->getMessage());
    log_message("Stack trace: " . $e->getTraceAsString());
    
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    exit(1);
}
