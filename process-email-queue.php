<?php
/**
 * Script Cron para processar fila de emails
 * Executar a cada 5 minutos via cron job
 * 
 * Adicionar ao crontab:
 * */5 * * * * php /caminho/para/process-email-queue.php
 */

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/email.php';

// Verificar se está sendo executado via CLI
if (php_sapi_name() !== 'cli') {
    // Permitir acesso via web apenas para testes (remover em produção)
    if (!isset($_GET['secret']) || $_GET['secret'] !== getenv('CRON_SECRET')) {
        http_response_code(403);
        die('Acesso negado');
    }
}

// Log início
$startTime = microtime(true);
echo "[" . date('Y-m-d H:i:s') . "] Iniciando processamento da fila de emails...\n";

try {
    // Criar instância do Email (sem usar fila para processar)
    $email = new Email(false);
    
    // Processar até 50 emails por execução
    $result = $email->processQueue(50);
    
    // Log resultado
    $executionTime = round(microtime(true) - $startTime, 2);
    
    echo "[" . date('Y-m-d H:i:s') . "] Processamento concluído:\n";
    echo "  - Processados: " . $result['processed'] . "\n";
    echo "  - Enviados: " . $result['sent'] . "\n";
    echo "  - Falharam: " . $result['failed'] . "\n";
    echo "  - Tempo: {$executionTime}s\n";
    
    // Se houver erro, registrar
    if (isset($result['error'])) {
        echo "  - Erro: " . $result['error'] . "\n";
    }
    
    // Registrar em arquivo de log
    $logFile = __DIR__ . '/logs/email-queue-' . date('Y-m-d') . '.log';
    $logMessage = sprintf(
        "[%s] Processed: %d, Sent: %d, Failed: %d, Time: %ss\n",
        date('Y-m-d H:i:s'),
        $result['processed'],
        $result['sent'],
        $result['failed'],
        $executionTime
    );
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    exit(0);
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERRO: " . $e->getMessage() . "\n";
    
    // Registrar erro em arquivo
    $logFile = __DIR__ . '/logs/email-queue-' . date('Y-m-d') . '.log';
    $logMessage = sprintf(
        "[%s] ERROR: %s\n",
        date('Y-m-d H:i:s'),
        $e->getMessage()
    );
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    exit(1);
}
