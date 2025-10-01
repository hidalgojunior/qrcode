<?php
/**
 * Test direct API call
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing API fetch...\n\n";

// Simulate the API call
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['action'] = 'fetch';

// Capture output
ob_start();

try {
    include __DIR__ . '/api/inbox.php';
    $output = ob_get_clean();
    
    echo "Output:\n";
    echo $output . "\n\n";
    
    if (!empty($output)) {
        $json = json_decode($output, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "JSON válido!\n";
            print_r($json);
        } else {
            echo "JSON inválido: " . json_last_error_msg() . "\n";
        }
    } else {
        echo "Nenhuma saída!\n";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
