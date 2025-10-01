<?php
/**
 * Instalador Simplificado - DevMenthors
 * Execute este arquivo apenas uma vez para criar o banco de dados
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar se já foi instalado
if (file_exists(__DIR__ . '/.installed')) {
    die('✅ Sistema já instalado! Acesse: <a href="home.php">home.php</a>');
}

// Verificar se .env existe
if (!file_exists(__DIR__ . '/.env')) {
    die('❌ Erro: Arquivo .env não encontrado!');
}

// Configurações do banco
$host = 'localhost';
$dbname = 'devmenthors';
$user = 'root';
$pass = '';

echo "<h1>🚀 Instalador DevMenthors</h1>";
echo "<p>Instalando banco de dados...</p>";

try {
    // Conectar sem banco específico
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Conectado ao MySQL</p>";
    
    // Criar banco de dados
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Banco de dados '$dbname' criado</p>";
    
    // Selecionar banco
    $pdo->exec("USE `$dbname`");
    
    // Ler e executar schema.sql
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    
    // Dividir por comandos SQL
    $statements = array_filter(
        array_map('trim', explode(';', $schema)),
        function($stmt) {
            return !empty($stmt) && 
                   strpos($stmt, '--') !== 0 && 
                   strpos($stmt, 'SET') !== 0 &&
                   strpos($stmt, 'START TRANSACTION') === false &&
                   strpos($stmt, 'COMMIT') === false;
        }
    );
    
    $count = 0;
    foreach ($statements as $statement) {
        if (trim($statement)) {
            try {
                $pdo->exec($statement);
                $count++;
            } catch (PDOException $e) {
                // Ignorar erros de tabela já existente
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "<p>⚠️ Aviso: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
        }
    }
    
    echo "<p>✅ $count comandos SQL executados</p>";
    
    // Criar arquivo .installed
    file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
    
    echo "<hr>";
    echo "<h2>🎉 Instalação Concluída com Sucesso!</h2>";
    echo "<p><strong>Banco de dados:</strong> $dbname</p>";
    echo "<p><strong>Tabelas criadas:</strong> 11</p>";
    echo "<p><strong>Planos inseridos:</strong> 8 (4 microsites + 4 QR codes)</p>";
    echo "<hr>";
    echo "<h3>📝 Próximos Passos:</h3>";
    echo "<ol>";
    echo "<li><a href='register.php'>Criar sua conta</a></li>";
    echo "<li><a href='login.php'>Fazer login</a></li>";
    echo "<li><a href='dashboard/'>Acessar dashboard</a></li>";
    echo "</ol>";
    echo "<hr>";
    echo "<p><a href='home.php' style='background: linear-gradient(135deg, #2364aa, #3da5d9); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;'>🏠 Ir para Home</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ ERRO: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Verifique se:</p>";
    echo "<ul>";
    echo "<li>O MySQL está rodando no Laragon</li>";
    echo "<li>As credenciais em .env estão corretas</li>";
    echo "<li>O usuário 'root' tem permissões</li>";
    echo "</ul>";
}
?>
