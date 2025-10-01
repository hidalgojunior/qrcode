<?php
/**
 * Script de diagnóstico para o sistema de inbox
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DIAGNÓSTICO DO SISTEMA DE INBOX ===\n\n";

// 1. Verificar extensão IMAP
echo "1. Extensão IMAP: ";
if (extension_loaded('imap')) {
    echo "✓ INSTALADA\n";
} else {
    echo "✗ NÃO INSTALADA\n";
    die("ERRO: Extensão PHP IMAP não está instalada!\n");
}

// 2. Verificar Composer autoload
echo "2. Composer autoload: ";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✓ ENCONTRADO\n";
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    echo "✗ NÃO ENCONTRADO\n";
    die("ERRO: Execute 'composer install'\n");
}

// 3. Verificar classe PhpImap
echo "3. Biblioteca PhpImap: ";
if (class_exists('PhpImap\Mailbox')) {
    echo "✓ CARREGADA\n";
} else {
    echo "✗ NÃO ENCONTRADA\n";
    die("ERRO: Biblioteca PhpImap não instalada. Execute 'composer require php-imap/php-imap'\n");
}

// 4. Verificar Database
echo "4. Classe Database: ";
require_once __DIR__ . '/includes/database.php';
if (class_exists('Database')) {
    echo "✓ CARREGADA\n";
} else {
    echo "✗ NÃO ENCONTRADA\n";
    die("ERRO: Classe Database não encontrada\n");
}

// 5. Testar conexão com banco
echo "5. Conexão com banco: ";
try {
    $db = Database::getInstance();
    echo "✓ CONECTADO\n";
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
    die();
}

// 6. Verificar tabelas
echo "6. Tabelas do inbox: ";
$tables = ['inbox_emails', 'inbox_replies'];
$allExist = true;
foreach ($tables as $table) {
    $result = $db->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows === 0) {
        echo "\n   ✗ Tabela '$table' não existe\n";
        $allExist = false;
    }
}
if ($allExist) {
    echo "✓ TODAS EXISTEM\n";
} else {
    echo "\n   Execute: php database/create-inbox-tables.php\n";
}

// 7. Verificar credenciais IMAP
echo "7. Credenciais IMAP: ";
$email = getenv('INBOX_EMAIL') ?: 'contato@devmenthors.shop';
$pass = getenv('INBOX_PASSWORD') ?: '@Jr34139251';
echo "\n   Email: $email\n";
echo "   Senha: " . str_repeat('*', strlen($pass)) . "\n";

// 8. Testar classe InboxManager
echo "8. Classe InboxManager: ";
try {
    require_once __DIR__ . '/includes/inbox.php';
    if (class_exists('InboxManager')) {
        echo "✓ CARREGADA\n";
        
        echo "9. Instanciar InboxManager: ";
        $inbox = new InboxManager();
        echo "✓ SUCESSO\n";
        
        echo "10. Testar conexão IMAP: ";
        $result = $inbox->testConnection();
        if ($result['success']) {
            echo "✓ CONECTADO\n";
            echo "\n   Mailbox: " . $result['info']['mailbox'] . "\n";
            echo "   Mensagens: " . $result['info']['messages'] . "\n";
            echo "   Não lidas: " . $result['info']['unread'] . "\n";
        } else {
            echo "✗ ERRO\n";
            echo "   " . $result['error'] . "\n";
        }
    } else {
        echo "✗ NÃO ENCONTRADA\n";
    }
} catch (Exception $e) {
    echo "✗ ERRO\n";
    echo "   Erro: " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n";
}

echo "\n=== DIAGNÓSTICO CONCLUÍDO ===\n";
