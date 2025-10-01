<?php
/**
 * Teste direto da função getStats que está causando problemas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TESTANDO getStats() ===\n\n";

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/inbox.php';
require_once __DIR__ . '/vendor/autoload.php';

try {
    echo "1. Criando InboxManager...\n";
    $inbox = new InboxManager();
    echo "   ✓ Sucesso\n\n";
    
    echo "2. Chamando getStats()...\n";
    $stats = $inbox->getStats();
    echo "   ✓ Sucesso\n\n";
    
    echo "3. Resultado:\n";
    echo json_encode($stats, JSON_PRETTY_PRINT) . "\n\n";
    
} catch (Exception $e) {
    echo "✗ ERRO:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
