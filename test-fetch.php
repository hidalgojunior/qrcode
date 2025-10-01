<?php
/**
 * Script para testar fetchNewEmails
 */

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/inbox.php';
require_once __DIR__ . '/vendor/autoload.php';

echo "=== TESTANDO FETCH EMAILS ===\n\n";

try {
    echo "1. Criando InboxManager...\n";
    $inbox = new InboxManager();
    echo "   ✓ Sucesso\n\n";
    
    echo "2. Buscando novos emails (limite: 50)...\n";
    $result = $inbox->fetchNewEmails(50);
    echo "   ✓ Função executada\n\n";
    
    echo "3. Resultado:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($result['success']) {
        echo "✓ SUCESSO!\n";
        echo "   Processados: {$result['processed']} de {$result['total']}\n";
    } else {
        echo "✗ ERRO!\n";
        echo "   " . $result['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERRO FATAL:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
