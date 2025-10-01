<?php
require_once __DIR__ . '/../includes/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$db = Database::getInstance();
$userId = $auth->getUserId();

// Filtros
$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$sortBy = $_GET['sort'] ?? 'created_at';
$sortOrder = $_GET['order'] ?? 'DESC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Construir query
$where = ["user_id = ?"];
$params = [$userId];

if ($search) {
    $where[] = "content LIKE ?";
    $params[] = "%$search%";
}

if ($type) {
    $where[] = "type = ?";
    $params[] = $type;
}

$whereClause = implode(' AND ', $where);

// Contar total
$total = $db->fetch(
    "SELECT COUNT(*) as count FROM qrcodes WHERE $whereClause",
    $params
)['count'];

$totalPages = ceil($total / $perPage);

// Buscar QR codes
$qrcodes = $db->fetchAll(
    "SELECT id, type, content, size, format, downloads, created_at 
     FROM qrcodes 
     WHERE $whereClause 
     ORDER BY $sortBy $sortOrder 
     LIMIT $perPage OFFSET $offset",
    $params
);

// Estatísticas
$stats = $db->fetch(
    "SELECT 
        COUNT(*) as total,
        SUM(downloads) as total_downloads,
        COUNT(DISTINCT type) as unique_types
     FROM qrcodes 
     WHERE user_id = ?",
    [$userId]
);

// QR Codes por tipo
$qrByType = $db->fetchAll(
    "SELECT type, COUNT(*) as count 
     FROM qrcodes 
     WHERE user_id = ? 
     GROUP BY type 
     ORDER BY count DESC",
    [$userId]
);

$typeLabels = [
    'text' => 'Texto',
    'url' => 'URL/Link',
    'email' => 'Email',
    'phone' => 'Telefone',
    'sms' => 'SMS',
    'wifi' => 'WiFi',
    'vcard' => 'vCard'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes - DevMenthors</title>
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
        .qr-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .qr-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
                        <i class="fas fa-qrcode gradient-text mr-2"></i>
                        Meus QR Codes
                    </h1>
                    <p class="text-gray-600">Histórico completo de todos os QR Codes gerados</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Gerados</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total'] ?? 0; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-qrcode text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Downloads</p>
                                <p class="text-2xl font-bold text-green-600">
                                    <?php echo number_format($stats['total_downloads'] ?? 0, 0, ',', '.'); ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-download text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Tipos Diferentes</p>
                                <p class="text-2xl font-bold text-purple-600">
                                    <?php echo $stats['unique_types'] ?? 0; ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-th-large text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Mais Popular</p>
                                <p class="text-lg font-bold text-orange-600">
                                    <?php 
                                    if (!empty($qrByType)) {
                                        echo $typeLabels[$qrByType[0]['type']] ?? $qrByType[0]['type'];
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-orange-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <form method="GET" class="flex flex-col lg:flex-row lg:items-center gap-4">
                        <!-- Search -->
                        <div class="flex-1">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" 
                                       name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       placeholder="Buscar no conteúdo..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <select name="type" 
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos os Tipos</option>
                            <?php foreach ($typeLabels as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo $type === $key ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Sort -->
                        <select name="sort" 
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="created_at" <?php echo $sortBy === 'created_at' ? 'selected' : ''; ?>>Mais Recentes</option>
                            <option value="downloads" <?php echo $sortBy === 'downloads' ? 'selected' : ''; ?>>Mais Baixados</option>
                            <option value="type" <?php echo $sortBy === 'type' ? 'selected' : ''; ?>>Tipo</option>
                        </select>

                        <button type="submit" 
                                class="gradient-bg text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                            <i class="fas fa-filter mr-2"></i>Filtrar
                        </button>

                        <a href="../index.php" 
                           class="bg-gray-800 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-900 transition">
                            <i class="fas fa-plus mr-2"></i>Novo QR Code
                        </a>
                    </form>
                </div>

                <!-- QR Codes by Type Chart -->
                <?php if (!empty($qrByType)): ?>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-chart-pie gradient-text mr-2"></i>
                        Distribuição por Tipo
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                        <?php foreach ($qrByType as $item): ?>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold gradient-text mb-2"><?php echo $item['count']; ?></div>
                            <div class="text-sm text-gray-600"><?php echo $typeLabels[$item['type']] ?? $item['type']; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- QR Codes Grid -->
                <?php if (empty($qrcodes)): ?>
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-qrcode text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum QR Code encontrado</h3>
                    <p class="text-gray-500 mb-6">
                        <?php echo $search ? 'Tente outra busca ou' : 'Comece gerando seu primeiro QR Code'; ?>
                    </p>
                    <a href="../index.php" class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Gerar QR Code
                    </a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
                    <?php foreach ($qrcodes as $qr): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden qr-card">
                        <!-- QR Code Preview -->
                        <div class="bg-gray-50 p-6 flex items-center justify-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($qr['content']); ?>" 
                                 alt="QR Code" 
                                 class="w-40 h-40">
                        </div>

                        <!-- Info -->
                        <div class="p-4">
                            <!-- Type Badge -->
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-3
                                <?php 
                                $colors = [
                                    'text' => 'bg-blue-100 text-blue-800',
                                    'url' => 'bg-green-100 text-green-800',
                                    'email' => 'bg-purple-100 text-purple-800',
                                    'phone' => 'bg-orange-100 text-orange-800',
                                    'sms' => 'bg-pink-100 text-pink-800',
                                    'wifi' => 'bg-indigo-100 text-indigo-800',
                                    'vcard' => 'bg-red-100 text-red-800'
                                ];
                                echo $colors[$qr['type']] ?? 'bg-gray-100 text-gray-800';
                                ?>">
                                <i class="fas fa-tag mr-1"></i>
                                <?php echo $typeLabels[$qr['type']] ?? $qr['type']; ?>
                            </span>

                            <!-- Content Preview -->
                            <p class="text-sm text-gray-700 mb-3 line-clamp-2 h-10">
                                <?php echo htmlspecialchars(substr($qr['content'], 0, 60)); ?>
                                <?php if (strlen($qr['content']) > 60) echo '...'; ?>
                            </p>

                            <!-- Stats -->
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                <span>
                                    <i class="fas fa-download mr-1"></i>
                                    <?php echo $qr['downloads']; ?> downloads
                                </span>
                                <span>
                                    <?php echo date('d/m/Y', strtotime($qr['created_at'])); ?>
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <button onclick="downloadQR(<?php echo $qr['id']; ?>, '<?php echo htmlspecialchars($qr['content'], ENT_QUOTES); ?>')" 
                                        class="flex-1 gradient-bg text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                                    <i class="fas fa-download mr-1"></i>Baixar
                                </button>
                                <button onclick="viewQR(<?php echo $qr['id']; ?>)" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="deleteQR(<?php echo $qr['id']; ?>)" 
                                        class="px-4 py-2 border border-red-300 rounded-lg text-sm font-semibold text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="bg-white rounded-lg shadow-md px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Mostrando <?php echo $offset + 1; ?> - <?php echo min($offset + $perPage, $total); ?> de <?php echo $total; ?> QR Codes
                        </div>
                        <div class="flex gap-2">
                            <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo $type; ?>&sort=<?php echo $sortBy; ?>" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-chevron-left mr-1"></i>Anterior
                            </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo $type; ?>&sort=<?php echo $sortBy; ?>" 
                               class="px-4 py-2 border rounded-lg text-sm font-semibold <?php echo $i === $page ? 'gradient-bg text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-100'; ?>">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo $type; ?>&sort=<?php echo $sortBy; ?>" 
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
        </main>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Detalhes do QR Code</h3>
                <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        function downloadQR(id, content) {
            // Incrementar contador de downloads
            fetch('../api/increment-qr-download.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });

            // Download do QR Code
            const url = `https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data=${encodeURIComponent(content)}`;
            const link = document.createElement('a');
            link.href = url;
            link.download = `qrcode-${id}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function viewQR(id) {
            fetch(`../api/get-qr-details.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const qr = data.qrcode;
                        document.getElementById('modalContent').innerHTML = `
                            <div class="text-center mb-6">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qr.content)}" 
                                     alt="QR Code" 
                                     class="mx-auto mb-4">
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">Tipo:</label>
                                    <p class="text-gray-900">${qr.type}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-600">Conteúdo:</label>
                                    <p class="text-gray-900 bg-gray-50 p-3 rounded break-all">${qr.content}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-semibold text-gray-600">Tamanho:</label>
                                        <p class="text-gray-900">${qr.size}px</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold text-gray-600">Formato:</label>
                                        <p class="text-gray-900">${qr.format.toUpperCase()}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold text-gray-600">Downloads:</label>
                                        <p class="text-gray-900">${qr.downloads}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold text-gray-600">Criado em:</label>
                                        <p class="text-gray-900">${new Date(qr.created_at).toLocaleDateString('pt-BR')}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.getElementById('viewModal').classList.remove('hidden');
                    }
                });
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }

        function deleteQR(id) {
            if (confirm('Tem certeza que deseja excluir este QR Code?')) {
                fetch('../api/delete-qrcode.php', {
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
                });
            }
        }
    </script>

</body>
</html>
