<?php
require_once __DIR__ . '/../includes/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$db = Database::getInstance();

// Buscar estatísticas do usuário
$userId = $auth->getUserId();

// Contar microsites
$micrositesCount = $db->fetch("SELECT COUNT(*) as count FROM microsites WHERE user_id = ?", [$userId])['count'];

// Contar QR codes
$qrcodesCount = $db->fetch("SELECT COUNT(*) as count FROM qrcodes WHERE user_id = ?", [$userId])['count'];

// Buscar microsites recentes
$microsites = $db->fetchAll(
    "SELECT id, slug, name, avatar, views, status, created_at 
     FROM microsites WHERE user_id = ? 
     ORDER BY created_at DESC LIMIT 5",
    [$userId]
);

// Buscar assinatura ativa
$subscription = $db->fetch(
    "SELECT s.*, p.name as plan_name, p.price 
     FROM subscriptions s 
     JOIN plans p ON s.plan_id = p.id 
     WHERE s.user_id = ? AND s.status = 'active' 
     ORDER BY s.end_date DESC LIMIT 1",
    [$userId]
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DevMenthors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --green-blue: #2364aa;
            --picton-blue: #3da5d9;
            --verdigris: #73bfb8;
            --mikado-yellow: #fec601;
            --pumpkin: #ea7317;
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

        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(35, 100, 170, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-8 ml-64">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Olá, <?php echo htmlspecialchars($user['name']); ?>! 👋
                </h1>
                <p class="text-gray-600">Bem-vindo ao seu painel de controle</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Microsites -->
                <div class="stat-card bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center">
                            <i class="fas fa-globe text-white text-xl"></i>
                        </div>
                        <span class="text-xs text-gray-500">
                            <?php echo $user['microsites_limit'] ?? 'Ilimitado'; ?>
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $micrositesCount; ?></h3>
                    <p class="text-sm text-gray-600">Microsites</p>
                </div>

                <!-- QR Codes -->
                <div class="stat-card bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-white text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $qrcodesCount; ?></h3>
                    <p class="text-sm text-gray-600">QR Codes</p>
                </div>

                <!-- Visualizações -->
                <div class="stat-card bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-eye text-white text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">
                        <?php 
                        $totalViews = $db->fetch("SELECT SUM(views) as total FROM microsites WHERE user_id = ?", [$userId])['total'] ?? 0;
                        echo number_format($totalViews);
                        ?>
                    </h3>
                    <p class="text-sm text-gray-600">Visualizações</p>
                </div>

                <!-- Plano -->
                <div class="stat-card bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-crown text-white text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800"><?php echo $user['plan_name']; ?></h3>
                    <p class="text-sm text-gray-600">Seu Plano</p>
                </div>
            </div>

            <!-- Assinatura Alert -->
            <?php if ($subscription && $subscription['status'] === 'active'): 
                $daysLeft = ceil((strtotime($subscription['end_date']) - time()) / 86400);
                if ($daysLeft <= 7):
            ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-4"></i>
                        <div>
                            <h4 class="font-bold text-yellow-800">Sua assinatura está próxima do vencimento</h4>
                            <p class="text-sm text-yellow-700">
                                Restam apenas <?php echo $daysLeft; ?> dias. Renove agora para não perder acesso!
                            </p>
                        </div>
                    </div>
                    <a href="subscription.php" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        Renovar Agora
                    </a>
                </div>
            </div>
            <?php endif; endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Microsites Recentes -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-globe mr-2"></i>Microsites Recentes
                        </h2>
                        <a href="../create-devmenthors.php" class="px-4 py-2 gradient-bg text-white rounded-lg hover:shadow-lg transition text-sm">
                            <i class="fas fa-plus mr-2"></i>Novo
                        </a>
                    </div>

                    <?php if (empty($microsites)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-globe text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 mb-4">Você ainda não criou nenhum microsite</p>
                        <a href="../create-devmenthors.php" class="inline-block px-6 py-3 gradient-bg text-white rounded-lg hover:shadow-lg transition">
                            Criar Primeiro Microsite
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($microsites as $microsite): ?>
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition">
                            <div class="flex items-center space-x-4">
                                <?php if ($microsite['avatar']): ?>
                                <img src="<?php echo htmlspecialchars($microsite['avatar']); ?>" 
                                     alt="Avatar" 
                                     class="w-12 h-12 rounded-full object-cover">
                                <?php else: ?>
                                <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center text-white font-bold">
                                    <?php echo strtoupper(substr($microsite['name'], 0, 1)); ?>
                                </div>
                                <?php endif; ?>
                                
                                <div>
                                    <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($microsite['name']); ?></h3>
                                    <p class="text-sm text-gray-500">
                                        /devmenthors/<?php echo htmlspecialchars($microsite['slug']); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-eye mr-1"></i><?php echo number_format($microsite['views']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($microsite['created_at'])); ?>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="../devmenthors.php?slug=<?php echo $microsite['slug']; ?>" 
                                       target="_blank"
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                       title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit-microsite.php?id=<?php echo $microsite['id']; ?>" 
                                       class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteMicrosite(<?php echo $microsite['id']; ?>)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <a href="microsites.php" class="text-blue-600 hover:text-blue-800">
                            Ver todos os microsites <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="space-y-6">
                    <!-- Criar QR Code -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-qrcode mr-2"></i>QR Code
                        </h2>
                        <p class="text-gray-600 text-sm mb-4">
                            Gere QR codes personalizados para seus links, contatos e muito mais
                        </p>
                        <a href="../index.php" 
                           class="block text-center px-4 py-3 border-2 border-blue-500 text-blue-600 rounded-lg hover:bg-blue-50 transition font-semibold">
                            Gerar QR Code
                        </a>
                    </div>

                    <!-- Upgrade Plan -->
                    <?php if ($user['plan_slug'] !== 'enterprise'): ?>
                    <div class="gradient-bg rounded-xl shadow-lg p-6 text-white">
                        <h2 class="text-xl font-bold mb-2">
                            <i class="fas fa-rocket mr-2"></i>Upgrade
                        </h2>
                        <p class="text-blue-100 text-sm mb-4">
                            Libere recursos premium e crie microsites ilimitados!
                        </p>
                        <a href="subscription.php" 
                           class="block text-center px-4 py-3 bg-yellow-400 text-gray-900 rounded-lg hover:bg-yellow-300 transition font-bold">
                            Ver Planos
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Suporte -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-life-ring mr-2"></i>Precisa de Ajuda?
                        </h2>
                        <p class="text-gray-600 text-sm mb-4">
                            Nossa equipe está pronta para ajudar você
                        </p>
                        <a href="support.php" 
                           class="block text-center px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-gray-400 transition font-semibold">
                            Falar com Suporte
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function deleteMicrosite(id) {
            if (confirm('Tem certeza que deseja excluir este microsite?')) {
                // TODO: Implementar exclusão
                console.log('Deleting microsite:', id);
            }
        }
    </script>
</body>
</html>
