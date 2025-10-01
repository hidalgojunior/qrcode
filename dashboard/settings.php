<?php
require_once __DIR__ . '/../includes/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$db = Database::getInstance();
$userId = $auth->getUserId();

$success = '';
$error = '';

// Buscar configurações atuais
$settings = $db->fetch("SELECT * FROM settings WHERE user_id = ?", [$userId]);

if (!$settings) {
    // Criar configurações padrão
    $db->query(
        "INSERT INTO settings (user_id, email_notifications, push_notifications, marketing_emails, weekly_reports, language, timezone) 
         VALUES (?, 1, 1, 0, 1, 'pt-BR', 'America/Sao_Paulo')",
        [$userId]
    );
    $settings = $db->fetch("SELECT * FROM settings WHERE user_id = ?", [$userId]);
}

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
    $pushNotifications = isset($_POST['push_notifications']) ? 1 : 0;
    $marketingEmails = isset($_POST['marketing_emails']) ? 1 : 0;
    $weeklyReports = isset($_POST['weekly_reports']) ? 1 : 0;
    $language = $_POST['language'] ?? 'pt-BR';
    $timezone = $_POST['timezone'] ?? 'America/Sao_Paulo';

    $updated = $db->query(
        "UPDATE settings SET 
            email_notifications = ?,
            push_notifications = ?,
            marketing_emails = ?,
            weekly_reports = ?,
            language = ?,
            timezone = ?,
            updated_at = NOW()
         WHERE user_id = ?",
        [$emailNotifications, $pushNotifications, $marketingEmails, $weeklyReports, $language, $timezone, $userId]
    );

    if ($updated) {
        $success = 'Configurações salvas com sucesso!';
        $settings = $db->fetch("SELECT * FROM settings WHERE user_id = ?", [$userId]);
    } else {
        $error = 'Erro ao salvar configurações';
    }
}

// Buscar notificações recentes
$notifications = $db->fetchAll(
    "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10",
    [$userId]
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - DevMenthors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --green-blue: #2364aa;
            --picton-blue: #3da5d9;
            --verdigris: #73bfb8;
            --mikado-yellow: #fec601;
            --eerie-black: #1a1a1a;
        }
        .gradient-bg {
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue), var(--verdigris));
        }
        .gradient-text {
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue), var(--verdigris));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .toggle-checkbox:checked {
            background-color: var(--picton-blue);
        }
        .toggle-checkbox:checked ~ .toggle-label {
            transform: translateX(1.5rem);
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <?php include 'includes/navbar.php'; ?>
    
    <div class="flex pt-16">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8">
            <div class="max-w-5xl mx-auto">
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-cog gradient-text mr-2"></i>
                        Configurações
                    </h1>
                    <p class="text-gray-600">Personalize suas preferências e notificações</p>
                </div>

                <!-- Alerts -->
                <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column - Settings Form -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <form method="POST">
                            <!-- Notifications -->
                            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-6">
                                    <i class="fas fa-bell gradient-text mr-2"></i>
                                    Notificações
                                </h3>

                                <!-- Email Notifications -->
                                <div class="flex items-center justify-between py-4 border-b border-gray-200">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Notificações por Email</h4>
                                        <p class="text-sm text-gray-600">Receba atualizações importantes no seu email</p>
                                    </div>
                                    <label class="relative inline-block w-12 h-6">
                                        <input type="checkbox" 
                                               name="email_notifications" 
                                               class="toggle-checkbox sr-only peer"
                                               <?php echo $settings['email_notifications'] ? 'checked' : ''; ?>>
                                        <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-500 transition"></div>
                                        <div class="toggle-label absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition transform"></div>
                                    </label>
                                </div>

                                <!-- Push Notifications -->
                                <div class="flex items-center justify-between py-4 border-b border-gray-200">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Notificações Push</h4>
                                        <p class="text-sm text-gray-600">Alertas em tempo real no navegador</p>
                                    </div>
                                    <label class="relative inline-block w-12 h-6">
                                        <input type="checkbox" 
                                               name="push_notifications" 
                                               class="toggle-checkbox sr-only peer"
                                               <?php echo $settings['push_notifications'] ? 'checked' : ''; ?>>
                                        <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-500 transition"></div>
                                        <div class="toggle-label absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition transform"></div>
                                    </label>
                                </div>

                                <!-- Marketing Emails -->
                                <div class="flex items-center justify-between py-4 border-b border-gray-200">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Emails de Marketing</h4>
                                        <p class="text-sm text-gray-600">Novidades, dicas e ofertas especiais</p>
                                    </div>
                                    <label class="relative inline-block w-12 h-6">
                                        <input type="checkbox" 
                                               name="marketing_emails" 
                                               class="toggle-checkbox sr-only peer"
                                               <?php echo $settings['marketing_emails'] ? 'checked' : ''; ?>>
                                        <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-500 transition"></div>
                                        <div class="toggle-label absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition transform"></div>
                                    </label>
                                </div>

                                <!-- Weekly Reports -->
                                <div class="flex items-center justify-between py-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Relatórios Semanais</h4>
                                        <p class="text-sm text-gray-600">Resumo semanal de atividades e estatísticas</p>
                                    </div>
                                    <label class="relative inline-block w-12 h-6">
                                        <input type="checkbox" 
                                               name="weekly_reports" 
                                               class="toggle-checkbox sr-only peer"
                                               <?php echo $settings['weekly_reports'] ? 'checked' : ''; ?>>
                                        <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-500 transition"></div>
                                        <div class="toggle-label absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition transform"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- Preferences -->
                            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-6">
                                    <i class="fas fa-sliders-h gradient-text mr-2"></i>
                                    Preferências
                                </h3>

                                <!-- Language -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-language mr-2"></i>Idioma
                                    </label>
                                    <select name="language" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="pt-BR" <?php echo $settings['language'] === 'pt-BR' ? 'selected' : ''; ?>>Português (Brasil)</option>
                                        <option value="en-US" <?php echo $settings['language'] === 'en-US' ? 'selected' : ''; ?>>English (US)</option>
                                        <option value="es-ES" <?php echo $settings['language'] === 'es-ES' ? 'selected' : ''; ?>>Español</option>
                                    </select>
                                </div>

                                <!-- Timezone -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-clock mr-2"></i>Fuso Horário
                                    </label>
                                    <select name="timezone" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="America/Sao_Paulo" <?php echo $settings['timezone'] === 'America/Sao_Paulo' ? 'selected' : ''; ?>>Brasília (GMT-3)</option>
                                        <option value="America/Manaus" <?php echo $settings['timezone'] === 'America/Manaus' ? 'selected' : ''; ?>>Manaus (GMT-4)</option>
                                        <option value="America/Noronha" <?php echo $settings['timezone'] === 'America/Noronha' ? 'selected' : ''; ?>>Fernando de Noronha (GMT-2)</option>
                                        <option value="America/New_York" <?php echo $settings['timezone'] === 'America/New_York' ? 'selected' : ''; ?>>New York (GMT-5)</option>
                                        <option value="Europe/London" <?php echo $settings['timezone'] === 'Europe/London' ? 'selected' : ''; ?>>London (GMT+0)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <button type="submit" 
                                    class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                                <i class="fas fa-save mr-2"></i>
                                Salvar Configurações
                            </button>
                        </form>

                        <!-- Data Management -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">
                                <i class="fas fa-database gradient-text mr-2"></i>
                                Gerenciamento de Dados
                            </h3>

                            <div class="space-y-4">
                                <button onclick="exportData()" 
                                        class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center">
                                        <i class="fas fa-download text-blue-600 text-xl mr-3"></i>
                                        <div class="text-left">
                                            <h4 class="font-semibold text-gray-900">Exportar Dados</h4>
                                            <p class="text-sm text-gray-600">Baixe todos os seus dados em formato JSON</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </button>

                                <button onclick="clearCache()" 
                                        class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center">
                                        <i class="fas fa-broom text-orange-600 text-xl mr-3"></i>
                                        <div class="text-left">
                                            <h4 class="font-semibold text-gray-900">Limpar Cache</h4>
                                            <p class="text-sm text-gray-600">Remover dados temporários e cache local</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </button>

                                <button onclick="clearNotifications()" 
                                        class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center">
                                        <i class="fas fa-bell-slash text-purple-600 text-xl mr-3"></i>
                                        <div class="text-left">
                                            <h4 class="font-semibold text-gray-900">Limpar Notificações</h4>
                                            <p class="text-sm text-gray-600">Remover todas as notificações lidas</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column - Recent Notifications -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-bell gradient-text mr-2"></i>
                                Notificações Recentes
                            </h3>

                            <?php if (empty($notifications)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-bell-slash text-4xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">Nenhuma notificação</p>
                            </div>
                            <?php else: ?>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                <?php foreach ($notifications as $notification): ?>
                                <div class="p-3 <?php echo $notification['is_read'] ? 'bg-gray-50' : 'bg-blue-50'; ?> rounded-lg">
                                    <div class="flex items-start justify-between mb-1">
                                        <span class="text-xs font-semibold text-gray-600">
                                            <?php 
                                            $types = [
                                                'payment' => 'Pagamento',
                                                'subscription' => 'Assinatura',
                                                'system' => 'Sistema'
                                            ];
                                            echo $types[$notification['type']] ?? $notification['type'];
                                            ?>
                                        </span>
                                        <?php if (!$notification['is_read']): ?>
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-900 mb-1">
                                        <?php echo htmlspecialchars($notification['title']); ?>
                                    </h4>
                                    <p class="text-xs text-gray-600 mb-2">
                                        <?php echo htmlspecialchars($notification['message']); ?>
                                    </p>
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?php 
                                        $time = strtotime($notification['created_at']);
                                        $diff = time() - $time;
                                        if ($diff < 60) echo 'agora';
                                        elseif ($diff < 3600) echo floor($diff/60) . ' min atrás';
                                        elseif ($diff < 86400) echo floor($diff/3600) . 'h atrás';
                                        else echo date('d/m/Y', $time);
                                        ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <button onclick="markAllAsRead()" 
                                    class="w-full mt-4 text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                Marcar todas como lidas
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <script>
        function exportData() {
            if (confirm('Deseja exportar todos os seus dados?')) {
                window.location.href = '../api/export-data.php';
            }
        }

        function clearCache() {
            if (confirm('Deseja limpar o cache local?')) {
                localStorage.clear();
                sessionStorage.clear();
                alert('Cache limpo com sucesso!');
                location.reload();
            }
        }

        function clearNotifications() {
            if (confirm('Deseja remover todas as notificações lidas?')) {
                fetch('../api/clear-notifications.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao limpar notificações');
                    }
                });
            }
        }

        function markAllAsRead() {
            fetch('../api/mark-notifications-read.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao marcar notificações');
                }
            });
        }
    </script>

</body>
</html>
