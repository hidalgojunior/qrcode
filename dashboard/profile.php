<?php
require_once __DIR__ . '/../includes/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$db = Database::getInstance();
$userId = $auth->getUserId();

$success = '';
$error = '';

// Processar atualização de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $avatar = $_POST['avatar'] ?? $user['avatar'];

        if (empty($name) || empty($email)) {
            $error = 'Nome e email são obrigatórios';
        } else {
            // Verificar se email já existe (exceto o atual)
            $existingUser = $db->fetch(
                "SELECT id FROM users WHERE email = ? AND id != ?",
                [$email, $userId]
            );

            if ($existingUser) {
                $error = 'Este email já está em uso';
            } else {
                $updated = $db->query(
                    "UPDATE users SET name = ?, email = ?, phone = ?, avatar = ? WHERE id = ?",
                    [$name, $email, $phone, $avatar, $userId]
                );

                if ($updated) {
                    $success = 'Perfil atualizado com sucesso!';
                    $user = $auth->getUser(); // Recarregar dados
                } else {
                    $error = 'Erro ao atualizar perfil';
                }
            }
        }
    } elseif ($_POST['action'] === 'change_password') {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Todos os campos de senha são obrigatórios';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'As senhas não conferem';
        } elseif (strlen($newPassword) < 8) {
            $error = 'A nova senha deve ter no mínimo 8 caracteres';
        } else {
            // Verificar senha atual
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $updated = $db->query(
                    "UPDATE users SET password = ? WHERE id = ?",
                    [$hashedPassword, $userId]
                );

                if ($updated) {
                    $success = 'Senha alterada com sucesso!';
                    
                    // Registrar auditoria
                    $db->query(
                        "INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)",
                        [$userId, 'password_change', 'Senha alterada pelo usuário', $_SERVER['REMOTE_ADDR']]
                    );
                } else {
                    $error = 'Erro ao alterar senha';
                }
            } else {
                $error = 'Senha atual incorreta';
            }
        }
    }
}

// Buscar estatísticas do usuário
$stats = $db->fetch(
    "SELECT 
        (SELECT COUNT(*) FROM microsites WHERE user_id = ?) as microsites,
        (SELECT COUNT(*) FROM qrcodes WHERE user_id = ?) as qrcodes,
        (SELECT SUM(views) FROM microsites WHERE user_id = ?) as total_views",
    [$userId, $userId, $userId]
);

// Buscar assinatura ativa
$subscription = $db->fetch(
    "SELECT s.*, p.name as plan_name, p.price, p.slug as plan_slug
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
    <title>Meu Perfil - DevMenthors</title>
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
        #avatarDropZone {
            transition: all 0.3s;
        }
        #avatarDropZone.dragover {
            border-color: var(--picton-blue);
            background-color: rgba(61, 165, 217, 0.1);
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
                        <i class="fas fa-user gradient-text mr-2"></i>
                        Meu Perfil
                    </h1>
                    <p class="text-gray-600">Gerencie suas informações pessoais e configurações de conta</p>
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
                    
                    <!-- Left Column - Profile Card -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="text-center mb-6">
                                <!-- Avatar -->
                                <div class="mb-4">
                                    <?php if ($user['avatar']): ?>
                                    <img id="avatarPreview" 
                                         src="<?php echo htmlspecialchars($user['avatar']); ?>" 
                                         alt="Avatar" 
                                         class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-gray-100 shadow-lg">
                                    <?php else: ?>
                                    <div id="avatarPreview" 
                                         class="w-32 h-32 gradient-bg rounded-full flex items-center justify-center text-white font-bold text-4xl mx-auto shadow-lg">
                                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <h2 class="text-xl font-bold text-gray-900 mb-1">
                                    <?php echo htmlspecialchars($user['name']); ?>
                                </h2>
                                <p class="text-gray-600 text-sm mb-4">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </p>

                                <!-- Plan Badge -->
                                <?php if ($subscription): ?>
                                <span class="inline-block px-4 py-2 gradient-bg text-white text-sm font-semibold rounded-full">
                                    <i class="fas fa-crown mr-1"></i>
                                    <?php echo htmlspecialchars($subscription['plan_name']); ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <hr class="my-6">

                            <!-- Stats -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">
                                        <i class="fas fa-globe mr-2"></i>Microsites
                                    </span>
                                    <span class="font-bold text-gray-900"><?php echo $stats['microsites']; ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">
                                        <i class="fas fa-qrcode mr-2"></i>QR Codes
                                    </span>
                                    <span class="font-bold text-gray-900"><?php echo $stats['qrcodes']; ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">
                                        <i class="fas fa-eye mr-2"></i>Visualizações
                                    </span>
                                    <span class="font-bold text-gray-900">
                                        <?php echo number_format($stats['total_views'] ?? 0, 0, ',', '.'); ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">
                                        <i class="fas fa-calendar mr-2"></i>Membro desde
                                    </span>
                                    <span class="font-bold text-gray-900">
                                        <?php echo date('m/Y', strtotime($user['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Forms -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Update Profile Form -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">
                                <i class="fas fa-user-edit gradient-text mr-2"></i>
                                Informações Pessoais
                            </h3>

                            <form method="POST" id="profileForm">
                                <input type="hidden" name="action" value="update_profile">
                                <input type="hidden" name="avatar" id="avatarInput" value="<?php echo htmlspecialchars($user['avatar']); ?>">

                                <!-- Avatar Upload -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Avatar
                                    </label>
                                    <div id="avatarDropZone" 
                                         class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600 mb-2">
                                            Arraste uma imagem ou clique para selecionar
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG ou GIF (máx. 2MB)</p>
                                        <input type="file" id="avatarFile" accept="image/*" class="hidden">
                                    </div>
                                </div>

                                <!-- Name -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nome Completo *
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>"
                                           required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Email -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email *
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>"
                                           required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Phone -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Telefone
                                    </label>
                                    <input type="tel" 
                                           name="phone" 
                                           id="phone"
                                           value="<?php echo htmlspecialchars($user['phone']); ?>"
                                           placeholder="(00) 00000-0000"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <button type="submit" 
                                        class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                                    <i class="fas fa-save mr-2"></i>
                                    Salvar Alterações
                                </button>
                            </form>
                        </div>

                        <!-- Change Password Form -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">
                                <i class="fas fa-lock gradient-text mr-2"></i>
                                Alterar Senha
                            </h3>

                            <form method="POST" id="passwordForm">
                                <input type="hidden" name="action" value="change_password">

                                <!-- Current Password -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Senha Atual *
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               name="current_password" 
                                               id="currentPassword"
                                               required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button type="button" 
                                                onclick="togglePassword('currentPassword')"
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- New Password -->
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nova Senha *
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               name="new_password" 
                                               id="newPassword"
                                               required
                                               minlength="8"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button type="button" 
                                                onclick="togglePassword('newPassword')"
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passwordStrength" class="mt-2 h-1 bg-gray-200 rounded-full overflow-hidden hidden">
                                        <div id="passwordStrengthBar" class="h-full transition-all"></div>
                                    </div>
                                    <p id="passwordStrengthText" class="text-xs mt-1"></p>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Confirmar Nova Senha *
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               name="confirm_password" 
                                               id="confirmPassword"
                                               required
                                               minlength="8"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button type="button" 
                                                onclick="togglePassword('confirmPassword')"
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" 
                                        class="w-full bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                                    <i class="fas fa-key mr-2"></i>
                                    Alterar Senha
                                </button>
                            </form>
                        </div>

                        <!-- Danger Zone -->
                        <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6">
                            <h3 class="text-xl font-bold text-red-900 mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Zona de Perigo
                            </h3>
                            <p class="text-red-700 text-sm mb-4">
                                Ações permanentes que não podem ser desfeitas
                            </p>
                            <button onclick="deleteAccount()" 
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-2"></i>
                                Excluir Conta
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        // Phone mask
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            if (value.length === 0) {
                e.target.value = '';
            } else if (value.length <= 2) {
                e.target.value = `(${value}`;
            } else if (value.length <= 6) {
                e.target.value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
            } else if (value.length <= 10) {
                e.target.value = `(${value.substring(0, 2)}) ${value.substring(2, 6)}-${value.substring(6)}`;
            } else {
                e.target.value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7)}`;
            }
        });

        // Avatar upload
        const dropZone = document.getElementById('avatarDropZone');
        const fileInput = document.getElementById('avatarFile');
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            handleFile(file);
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            handleFile(file);
        });

        function handleFile(file) {
            if (!file || !file.type.startsWith('image/')) {
                alert('Por favor, selecione uma imagem válida');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('A imagem deve ter no máximo 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const base64 = e.target.result;
                avatarInput.value = base64;
                
                if (avatarPreview.tagName === 'IMG') {
                    avatarPreview.src = base64;
                } else {
                    const img = document.createElement('img');
                    img.src = base64;
                    img.className = 'w-32 h-32 rounded-full object-cover mx-auto border-4 border-gray-100 shadow-lg';
                    avatarPreview.parentNode.replaceChild(img, avatarPreview);
                }
            };
            reader.readAsDataURL(file);
        }

        // Password toggle
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = event.target;
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength
        document.getElementById('newPassword').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            const strengthContainer = document.getElementById('passwordStrength');

            if (password.length === 0) {
                strengthContainer.classList.add('hidden');
                return;
            }

            strengthContainer.classList.remove('hidden');

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
            const texts = ['Fraca', 'Média', 'Boa', 'Forte'];
            const widths = ['25%', '50%', '75%', '100%'];

            strengthBar.className = `h-full transition-all ${colors[strength]}`;
            strengthBar.style.width = widths[strength];
            strengthText.textContent = `Senha: ${texts[strength]}`;
            strengthText.className = `text-xs mt-1 ${colors[strength].replace('bg-', 'text-')}`;
        });

        // Delete account
        function deleteAccount() {
            if (confirm('⚠️ ATENÇÃO! Esta ação é PERMANENTE e IRREVERSÍVEL.\n\nTodos os seus microsites, QR codes e dados serão excluídos.\n\nDeseja realmente continuar?')) {
                const email = prompt('Digite seu email para confirmar a exclusão:');
                if (email === '<?php echo $user['email']; ?>') {
                    window.location.href = '../api/delete-account.php';
                } else if (email !== null) {
                    alert('Email incorreto. Exclusão cancelada.');
                }
            }
        }
    </script>

</body>
</html>
