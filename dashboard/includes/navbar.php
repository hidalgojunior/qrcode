<nav class="bg-white shadow-lg fixed w-full top-0 z-50">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="../home.php" class="flex items-center">
                    <i class="fas fa-qrcode text-3xl mr-3 gradient-text"></i>
                    <span class="text-2xl font-bold gradient-text">DevMenthors</span>
                </a>
            </div>

            <!-- Right Menu -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button class="p-2 text-gray-600 hover:text-gray-800 relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition">
                        <?php if ($user['avatar']): ?>
                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" 
                             alt="Avatar" 
                             class="w-8 h-8 rounded-full object-cover <?php echo $auth->isSuperAdmin() ? 'ring-2 ring-yellow-400' : ''; ?>">
                        <?php else: ?>
                        <div class="w-8 h-8 <?php echo $auth->isSuperAdmin() ? 'bg-gradient-to-r from-red-500 to-purple-500 ring-2 ring-yellow-400' : 'gradient-bg'; ?> rounded-full flex items-center justify-center text-white font-bold text-sm">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <?php endif; ?>
                        <div class="flex flex-col items-start">
                            <span class="text-sm font-semibold text-gray-700">
                                <?php echo htmlspecialchars($user['name']); ?>
                            </span>
                            <?php if ($auth->isSuperAdmin()): ?>
                            <span class="text-xs font-bold text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-purple-500">
                                👑 SUPER ADMIN
                            </span>
                            <?php endif; ?>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50">
                        <?php if ($auth->isSuperAdmin()): ?>
                        <div class="px-4 py-2 bg-gradient-to-r from-red-100 to-purple-100 border-b">
                            <span class="text-xs font-bold text-purple-700">🔥 Super Admin</span>
                        </div>
                        <?php endif; ?>
                        <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i>Meu Perfil
                        </a>
                        <a href="subscription.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-crown mr-2"></i>Assinatura
                        </a>
                        <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>Configurações
                        </a>
                        <hr class="my-2">
                        <a href="../api/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
