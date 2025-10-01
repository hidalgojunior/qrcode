<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="fixed left-0 top-16 h-full w-64 bg-white shadow-lg z-40">
    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="index.php" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?php echo $currentPage === 'index.php' ? 'gradient-bg text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-home text-lg"></i>
                    <span class="font-semibold">Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="microsites.php" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?php echo $currentPage === 'microsites.php' ? 'gradient-bg text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-globe text-lg"></i>
                    <span class="font-semibold">Microsites</span>
                </a>
            </li>
            
            <li>
                <a href="qrcodes.php" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?php echo $currentPage === 'qrcodes.php' ? 'gradient-bg text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-qrcode text-lg"></i>
                    <span class="font-semibold">QR Codes</span>
                </a>
            </li>
            
            <li>
                <a href="analytics.php" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?php echo $currentPage === 'analytics.php' ? 'gradient-bg text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-chart-line text-lg"></i>
                    <span class="font-semibold">Analíticas</span>
                </a>
            </li>
            
            <hr class="my-4 border-gray-200">
            
            <li>
                <a href="subscription.php" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?php echo $currentPage === 'subscription.php' ? 'gradient-bg text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-crown text-lg"></i>
                    <span class="font-semibold">Assinatura</span>
                    <?php if ($user['plan_slug'] === 'basico'): ?>
                    <span class="ml-auto bg-yellow-400 text-gray-900 text-xs px-2 py-1 rounded-full font-bold">
                        PRO
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li>
                <a href="profile.php" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?php echo $currentPage === 'profile.php' ? 'gradient-bg text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-user text-lg"></i>
                    <span class="font-semibold">Perfil</span>
                </a>
            </li>
            
            <li>
                <a href="settings.php" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?php echo $currentPage === 'settings.php' ? 'gradient-bg text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-cog text-lg"></i>
                    <span class="font-semibold">Configurações</span>
                </a>
            </li>
            
            <hr class="my-4 border-gray-200">
            
            <li>
                <a href="support.php" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg transition <?php echo $currentPage === 'support.php' ? 'gradient-bg text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-life-ring text-lg"></i>
                    <span class="font-semibold">Suporte</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Current Plan Badge -->
    <div class="absolute bottom-4 left-4 right-4">
        <?php if ($auth->isSuperAdmin()): ?>
        <!-- Super Admin Badge -->
        <div class="bg-gradient-to-r from-red-500 via-purple-500 to-blue-500 rounded-lg p-4 border-2 border-yellow-400 shadow-xl animate-pulse">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-bold text-white">🔥 SUPER ADMIN</span>
                <i class="fas fa-shield-alt text-yellow-300 text-xl"></i>
            </div>
            <p class="text-lg font-bold text-white">Acesso Total</p>
            <p class="text-xs text-yellow-100 mt-1">
                ∞ Microsites | ∞ QR Codes | Recursos Premium
            </p>
        </div>
        <?php else: ?>
        <!-- Regular User Badge -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-4 border border-blue-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700">Plano Atual</span>
                <i class="fas fa-crown text-yellow-500"></i>
            </div>
            <p class="text-lg font-bold gradient-text"><?php echo $user['plan_name']; ?></p>
            <?php if ($user['microsites_limit']): ?>
            <p class="text-xs text-gray-600 mt-1">
                <?php echo $micrositesCount; ?>/<?php echo $user['microsites_limit']; ?> microsites
            </p>
            <?php else: ?>
            <p class="text-xs text-gray-600 mt-1">
                ∞ Microsites ilimitados
            </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</aside>
