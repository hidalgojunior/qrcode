<?php
/**
 * Instalador Automático DevMenthors
 * Este arquivo instala automaticamente o banco de dados
 */

// Verificar se já foi instalado
if (file_exists(__DIR__ . '/.installed')) {
    die('Sistema já foi instalado! Para reinstalar, delete o arquivo .installed');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        // Validar conexão
        $host = $_POST['db_host'] ?? 'localhost';
        $user = $_POST['db_user'] ?? 'root';
        $pass = $_POST['db_pass'] ?? '';
        $name = $_POST['db_name'] ?? 'devmenthors';
        
        try {
            $conn = new PDO("mysql:host=$host", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Salvar credenciais temporárias
            $_SESSION['install'] = [
                'db_host' => $host,
                'db_user' => $user,
                'db_pass' => $pass,
                'db_name' => $name
            ];
            
            $success = 'Conexão bem-sucedida! Avançando...';
            header('Location: install.php?step=2');
            exit;
        } catch (PDOException $e) {
            $error = 'Erro ao conectar: ' . $e->getMessage();
        }
    } elseif ($step == 2) {
        // Criar banco e tabelas
        session_start();
        $config = $_SESSION['install'] ?? null;
        
        if (!$config) {
            header('Location: install.php?step=1');
            exit;
        }
        
        try {
            $conn = new PDO("mysql:host={$config['db_host']}", $config['db_user'], $config['db_pass']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Criar banco de dados
            $conn->exec("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->exec("USE `{$config['db_name']}`");
            
            // Ler e executar schema.sql
            $schema = file_get_contents(__DIR__ . '/database/schema.sql');
            
            // Remover comentários e dividir em queries
            $queries = array_filter(
                array_map('trim', 
                    explode(';', 
                        preg_replace('/^--.*$/m', '', $schema)
                    )
                ),
                'strlen'
            );
            
            foreach ($queries as $query) {
                if (!empty(trim($query))) {
                    $conn->exec($query);
                }
            }
            
            // Criar arquivo .env
            $envContent = "DB_HOST={$config['db_host']}\n";
            $envContent .= "DB_NAME={$config['db_name']}\n";
            $envContent .= "DB_USER={$config['db_user']}\n";
            $envContent .= "DB_PASS={$config['db_pass']}\n";
            $envContent .= "BASE_URL=http://localhost/QrCode\n";
            
            file_put_contents(__DIR__ . '/.env', $envContent);
            
            // Criar arquivo .installed
            file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
            
            // Limpar sessão
            unset($_SESSION['install']);
            
            header('Location: install.php?step=3');
            exit;
        } catch (Exception $e) {
            $error = 'Erro ao instalar: ' . $e->getMessage();
        }
    }
}

session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação DevMenthors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --green-blue: #2364aa;
            --picton-blue: #3da5d9;
        }
        .gradient-bg {
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue));
        }
        .gradient-text {
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 max-w-2xl">
        <!-- Logo -->
        <div class="text-center mb-8">
            <i class="fas fa-qrcode text-6xl gradient-text mb-4"></i>
            <h1 class="text-4xl font-bold gradient-text">DevMenthors</h1>
            <p class="text-gray-600 mt-2">Instalação do Sistema</p>
        </div>

        <!-- Progress -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center <?php echo $step >= 1 ? 'text-blue-600' : 'text-gray-400'; ?>">
                    <div class="w-8 h-8 rounded-full <?php echo $step >= 1 ? 'gradient-bg text-white' : 'bg-gray-300'; ?> flex items-center justify-center mr-2">
                        <?php echo $step > 1 ? '<i class="fas fa-check"></i>' : '1'; ?>
                    </div>
                    <span class="font-semibold">Configuração</span>
                </div>
                
                <div class="flex-1 h-1 mx-4 <?php echo $step >= 2 ? 'gradient-bg' : 'bg-gray-300'; ?>"></div>
                
                <div class="flex items-center <?php echo $step >= 2 ? 'text-blue-600' : 'text-gray-400'; ?>">
                    <div class="w-8 h-8 rounded-full <?php echo $step >= 2 ? 'gradient-bg text-white' : 'bg-gray-300'; ?> flex items-center justify-center mr-2">
                        <?php echo $step > 2 ? '<i class="fas fa-check"></i>' : '2'; ?>
                    </div>
                    <span class="font-semibold">Instalação</span>
                </div>
                
                <div class="flex-1 h-1 mx-4 <?php echo $step >= 3 ? 'gradient-bg' : 'bg-gray-300'; ?>"></div>
                
                <div class="flex items-center <?php echo $step >= 3 ? 'text-blue-600' : 'text-gray-400'; ?>">
                    <div class="w-8 h-8 rounded-full <?php echo $step >= 3 ? 'gradient-bg text-white' : 'bg-gray-300'; ?> flex items-center justify-center mr-2">
                        <?php echo $step >= 3 ? '<i class="fas fa-check"></i>' : '3'; ?>
                    </div>
                    <span class="font-semibold">Concluído</span>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-xl"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Step Content -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <?php if ($step == 1): ?>
            <!-- Step 1: Database Configuration -->
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-database mr-2"></i>Configuração do Banco de Dados
            </h2>
            
            <p class="text-gray-600 mb-6">
                Insira as credenciais do seu banco de dados MySQL. Se o banco não existir, ele será criado automaticamente.
            </p>

            <form method="POST" action="install.php?step=1">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-server mr-2"></i>Servidor (Host)
                        </label>
                        <input type="text" name="db_host" value="localhost" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Geralmente "localhost" ou "127.0.0.1"</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Usuário
                        </label>
                        <input type="text" name="db_user" value="root" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-key mr-2"></i>Senha
                        </label>
                        <input type="password" name="db_pass" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Deixe em branco se não houver senha</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-database mr-2"></i>Nome do Banco
                        </label>
                        <input type="text" name="db_name" value="devmenthors" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full mt-6 py-3 gradient-bg text-white rounded-lg font-bold hover:shadow-lg transition">
                    <i class="fas fa-arrow-right mr-2"></i>Testar Conexão e Continuar
                </button>
            </form>

            <?php elseif ($step == 2): ?>
            <!-- Step 2: Installation -->
            <div class="text-center py-8">
                <div class="animate-spin w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-6"></div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Instalando...</h2>
                <p class="text-gray-600">
                    Criando banco de dados e tabelas. Por favor, aguarde...
                </p>
            </div>

            <script>
                // Auto-submit para iniciar instalação
                setTimeout(() => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'install.php?step=2';
                    document.body.appendChild(form);
                    form.submit();
                }, 1000);
            </script>

            <?php elseif ($step == 3): ?>
            <!-- Step 3: Complete -->
            <div class="text-center py-8">
                <div class="w-20 h-20 gradient-bg rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-4xl text-white"></i>
                </div>
                
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    Instalação Concluída! 🎉
                </h2>
                
                <p class="text-gray-600 mb-8">
                    Seu sistema DevMenthors está pronto para uso!
                </p>

                <div class="bg-blue-50 rounded-lg p-6 mb-6 text-left">
                    <h3 class="font-bold text-gray-800 mb-3">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Informações Importantes
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Banco de dados criado com sucesso</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Tabelas instaladas</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Planos configurados</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Arquivo .env criado</li>
                    </ul>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8 text-left">
                    <h4 class="font-bold text-yellow-800 mb-2">
                        <i class="fas fa-shield-alt mr-2"></i>Segurança
                    </h4>
                    <p class="text-sm text-yellow-700">
                        <strong>IMPORTANTE:</strong> Delete o arquivo <code class="bg-yellow-100 px-2 py-1 rounded">install.php</code> 
                        por segurança após a instalação!
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="register.php" 
                       class="flex-1 py-3 gradient-bg text-white rounded-lg font-bold hover:shadow-lg transition text-center">
                        <i class="fas fa-user-plus mr-2"></i>Criar Conta
                    </a>
                    <a href="home.php" 
                       class="flex-1 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-bold hover:border-blue-500 transition text-center">
                        <i class="fas fa-home mr-2"></i>Ir para Home
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600">
            <p class="text-sm">
                Precisa de ajuda? Consulte a 
                <a href="INSTALL-DATABASE.md" class="text-blue-600 hover:underline">documentação</a>
            </p>
        </div>
    </div>
</body>
</html>
