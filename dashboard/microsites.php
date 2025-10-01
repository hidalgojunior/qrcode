<?php
require_once __DIR__ . '/../includes/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$db = Database::getInstance();
$userId = $auth->getUserId();

// Filtros
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$sortBy = $_GET['sort'] ?? 'created_at';
$sortOrder = $_GET['order'] ?? 'DESC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Construir query
$where = ["user_id = ?"];
$params = [$userId];

if ($search) {
    $where[] = "(name LIKE ? OR slug LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $where[] = "status = ?";
    $params[] = $status;
}

$whereClause = implode(' AND ', $where);

// Contar total
$total = $db->fetch(
    "SELECT COUNT(*) as count FROM microsites WHERE $whereClause",
    $params
)['count'];

$totalPages = ceil($total / $perPage);

// Buscar microsites
$microsites = $db->fetchAll(
    "SELECT id, slug, name, avatar, views, status, created_at, updated_at 
     FROM microsites 
     WHERE $whereClause 
     ORDER BY $sortBy $sortOrder 
     LIMIT $perPage OFFSET $offset",
    $params
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microsites - DevMenthors</title>
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
    </style>
</head>
<body class="bg-gray-50">
    
    <?php include 'includes/navbar.php'; ?>
    
    <div class="flex pt-16">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8">
            <div class="max-w-7xl mx-auto">
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-globe gradient-text mr-2"></i>
                        Meus Microsites
                    </h1>
                    <p class="text-gray-600">Gerencie todos os seus microsites em um só lugar</p>
                </div>

                <!-- Actions Bar -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Search -->
                        <form method="GET" class="flex-1">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" 
                                       name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       placeholder="Buscar por nome ou URL..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </form>

                        <!-- Filters -->
                        <div class="flex gap-3">
                            <select name="status" 
                                    onchange="this.form.submit()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Todos Status</option>
                                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Ativos</option>
                                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inativos</option>
                            </select>

                            <select name="sort" 
                                    onchange="this.form.submit()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="created_at" <?php echo $sortBy === 'created_at' ? 'selected' : ''; ?>>Mais Recentes</option>
                                <option value="name" <?php echo $sortBy === 'name' ? 'selected' : ''; ?>>Nome (A-Z)</option>
                                <option value="views" <?php echo $sortBy === 'views' ? 'selected' : ''; ?>>Mais Vistos</option>
                            </select>

                            <a href="../create-devmenthors.php" 
                               class="gradient-bg text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Novo Microsite
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $total; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-globe text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Ativos</p>
                                <p class="text-2xl font-bold text-green-600">
                                    <?php 
                                    $active = $db->fetch("SELECT COUNT(*) as count FROM microsites WHERE user_id = ? AND status = 'active'", [$userId])['count'];
                                    echo $active;
                                    ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Visitas Total</p>
                                <p class="text-2xl font-bold text-purple-600">
                                    <?php 
                                    $totalViews = $db->fetch("SELECT SUM(views) as total FROM microsites WHERE user_id = ?", [$userId])['total'] ?? 0;
                                    echo number_format($totalViews, 0, ',', '.');
                                    ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-eye text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Média Visitas</p>
                                <p class="text-2xl font-bold text-orange-600">
                                    <?php 
                                    $avgViews = $total > 0 ? round($totalViews / $total) : 0;
                                    echo number_format($avgViews, 0, ',', '.');
                                    ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <?php if (empty($microsites)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-globe text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum microsite encontrado</h3>
                        <p class="text-gray-500 mb-6">
                            <?php echo $search ? 'Tente outra busca ou' : 'Comece criando seu primeiro microsite'; ?>
                        </p>
                        <a href="../create-devmenthors.php" class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Criar Microsite
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Microsite
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        URL
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Visitas
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Criado
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($microsites as $microsite): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <?php if ($microsite['avatar']): ?>
                                            <img src="<?php echo htmlspecialchars($microsite['avatar']); ?>" 
                                                 alt="Avatar" 
                                                 class="w-10 h-10 rounded-full object-cover mr-3">
                                            <?php else: ?>
                                            <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                <?php echo strtoupper(substr($microsite['name'], 0, 1)); ?>
                                            </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    <?php echo htmlspecialchars($microsite['name']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="../devmenthors/<?php echo htmlspecialchars($microsite['slug']); ?>" 
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                            /<?php echo htmlspecialchars($microsite['slug']); ?>
                                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php if ($microsite['status'] === 'active'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Ativo
                                        </span>
                                        <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-pause-circle mr-1"></i>Inativo
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-semibold text-gray-900">
                                            <?php echo number_format($microsite['views'], 0, ',', '.'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-600">
                                            <?php echo date('d/m/Y', strtotime($microsite['created_at'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="../devmenthors/<?php echo htmlspecialchars($microsite['slug']); ?>" 
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-800 p-2" 
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="../create-devmenthors.php?edit=<?php echo $microsite['id']; ?>" 
                                               class="text-green-600 hover:text-green-800 p-2" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="shareMicrosite('<?php echo htmlspecialchars($microsite['slug']); ?>')"
                                                    class="text-purple-600 hover:text-purple-800 p-2" 
                                                    title="Compartilhar">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                            <button onclick="toggleStatus(<?php echo $microsite['id']; ?>, '<?php echo $microsite['status']; ?>')"
                                                    class="text-orange-600 hover:text-orange-800 p-2" 
                                                    title="<?php echo $microsite['status'] === 'active' ? 'Desativar' : 'Ativar'; ?>">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                            <button onclick="deleteMicrosite(<?php echo $microsite['id']; ?>, '<?php echo htmlspecialchars($microsite['name']); ?>')"
                                                    class="text-red-600 hover:text-red-800 p-2" 
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Mostrando <?php echo $offset + 1; ?> - <?php echo min($offset + $perPage, $total); ?> de <?php echo $total; ?> resultados
                            </div>
                            <div class="flex gap-2">
                                <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&sort=<?php echo $sortBy; ?>" 
                                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-chevron-left mr-1"></i>Anterior
                                </a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&sort=<?php echo $sortBy; ?>" 
                                   class="px-4 py-2 border rounded-lg text-sm font-semibold <?php echo $i === $page ? 'gradient-bg text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-100'; ?>">
                                    <?php echo $i; ?>
                                </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&sort=<?php echo $sortBy; ?>" 
                                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100">
                                    Próximo<i class="fas fa-chevron-right ml-1"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <script>
        function shareMicrosite(slug) {
            const url = `${window.location.origin}/devmenthors/${slug}`;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Meu Microsite',
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url);
                alert('Link copiado para área de transferência!');
            }
        }

        function toggleStatus(id, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const action = newStatus === 'active' ? 'ativar' : 'desativar';
            
            if (confirm(`Deseja ${action} este microsite?`)) {
                fetch('../api/toggle-microsite-status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Erro ao atualizar status');
                });
            }
        }

        function deleteMicrosite(id, name) {
            if (confirm(`Tem certeza que deseja excluir "${name}"?\n\nEsta ação não pode ser desfeita!`)) {
                if (confirm('Confirma exclusão permanente?')) {
                    fetch('../api/delete-microsite.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Erro ao excluir microsite');
                    });
                }
            }
        }
    </script>

</body>
</html>
