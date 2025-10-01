<?php
require_once 'config.php';

// Pegar ID do microsite da URL
$micrositeId = $_GET['id'] ?? null;

if (!$micrositeId) {
    header('Location: index.php');
    exit;
}

// Buscar dados do microsite
$micrositeFile = MICROSITE_DIR . '/' . $micrositeId . '.json';

if (!file_exists($micrositeFile)) {
    header('Location: index.php');
    exit;
}

$data = json_decode(file_get_contents($micrositeFile), true);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['name'] ?? 'Microsite'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, <?php echo $data['theme']['gradient_start'] ?? '#667eea'; ?> 0%, <?php echo $data['theme']['gradient_end'] ?? '#764ba2'; ?> 100%);
        }
        .link-button {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .link-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Card Principal -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 mb-6">
            <!-- Avatar/Logo -->
            <?php if (!empty($data['avatar'])): ?>
            <div class="flex justify-center mb-6">
                <img src="<?php echo htmlspecialchars($data['avatar']); ?>" 
                     alt="Avatar" 
                     class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
            </div>
            <?php endif; ?>

            <!-- Nome e Descrição -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <?php echo htmlspecialchars($data['name'] ?? 'Nome'); ?>
                </h1>
                <?php if (!empty($data['title'])): ?>
                <p class="text-lg text-gray-600 mb-3">
                    <?php echo htmlspecialchars($data['title']); ?>
                </p>
                <?php endif; ?>
                <?php if (!empty($data['bio'])): ?>
                <p class="text-gray-600">
                    <?php echo nl2br(htmlspecialchars($data['bio'])); ?>
                </p>
                <?php endif; ?>
            </div>

            <!-- Redes Sociais -->
            <?php if (!empty($data['social'])): ?>
            <div class="flex justify-center gap-4 mb-8 flex-wrap">
                <?php foreach ($data['social'] as $social): ?>
                    <?php if (!empty($social['url'])): ?>
                    <a href="<?php echo htmlspecialchars($social['url']); ?>" 
                       target="_blank"
                       class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition text-gray-700 text-xl">
                        <i class="<?php echo htmlspecialchars($social['icon']); ?>"></i>
                    </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Links/Botões/Widgets -->
            <?php if (!empty($data['widgets'])): ?>
            <div class="space-y-4">
                <?php foreach ($data['widgets'] as $widget): ?>
                    <?php 
                    switch($widget['type']):
                        case 'link':
                            if (!empty($widget['url'])):
                    ?>
                    <a href="<?php echo htmlspecialchars($widget['url']); ?>" 
                       target="_blank"
                       class="link-button block w-full py-4 px-6 rounded-xl font-semibold text-center shadow-md"
                       style="background-color: <?php echo htmlspecialchars($widget['color'] ?? '#667eea'); ?>; color: white;">
                        <?php if (!empty($widget['icon'])): ?>
                        <i class="<?php echo htmlspecialchars($widget['icon']); ?> mr-2"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($widget['title']); ?>
                    </a>
                    <?php 
                            endif;
                            break;
                            
                        case 'pix':
                            if (!empty($widget['pix_key'])):
                    ?>
                    <div class="bg-green-50 border-2 border-green-500 rounded-xl p-6">
                        <div class="text-center mb-4">
                            <i class="fas fa-money-bill text-5xl text-green-600 mb-3"></i>
                            <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($widget['title'] ?? 'PIX'); ?></h3>
                            <?php if (!empty($widget['pix_name'])): ?>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($widget['pix_name']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-xs text-gray-500 mb-2">Chave PIX (<?php echo htmlspecialchars($widget['pix_type'] ?? 'random'); ?>):</p>
                            <p class="font-mono text-sm text-gray-800 break-all"><?php echo htmlspecialchars($widget['pix_key']); ?></p>
                            <button onclick="copyPix('<?php echo htmlspecialchars($widget['pix_key']); ?>')" 
                                    class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition">
                                <i class="fas fa-copy mr-2"></i>Copiar Chave
                            </button>
                        </div>
                    </div>
                    <?php 
                            endif;
                            break;
                            
                        case 'gallery':
                            if (!empty($widget['images'])):
                                $images = array_filter(explode("\n", $widget['images']));
                                if (count($images) > 0):
                    ?>
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <?php if (!empty($widget['title'])): ?>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4"><?php echo htmlspecialchars($widget['title']); ?></h3>
                        <?php endif; ?>
                        <div class="grid grid-cols-3 gap-3">
                            <?php foreach ($images as $image): ?>
                                <?php if (trim($image)): ?>
                                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                                    <img src="<?php echo htmlspecialchars(trim($image)); ?>" 
                                         alt="Galeria" 
                                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-300 cursor-pointer"
                                         onclick="openImage('<?php echo htmlspecialchars(trim($image)); ?>')">
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php 
                                endif;
                            endif;
                            break;
                            
                        case 'music':
                            if (!empty($widget['title'])):
                    ?>
                    <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-xl p-6 text-white">
                        <div class="flex items-center gap-4 mb-4">
                            <?php if (!empty($widget['cover'])): ?>
                            <img src="<?php echo htmlspecialchars($widget['cover']); ?>" 
                                 alt="Capa" 
                                 class="w-20 h-20 rounded-lg shadow-lg object-cover">
                            <?php else: ?>
                            <div class="w-20 h-20 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-music text-3xl"></i>
                            </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold"><?php echo htmlspecialchars($widget['title']); ?></h3>
                                <?php if (!empty($widget['artist'])): ?>
                                <p class="text-sm opacity-90"><?php echo htmlspecialchars($widget['artist']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($widget['music_url'])): ?>
                        <a href="<?php echo htmlspecialchars($widget['music_url']); ?>" 
                           target="_blank"
                           class="block w-full bg-white/20 hover:bg-white/30 py-3 px-6 rounded-lg text-center font-semibold transition">
                            <i class="fas fa-play-circle mr-2"></i>Ouvir Agora
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php 
                            endif;
                            break;
                            
                        case 'video':
                            if (!empty($widget['video_id'])):
                    ?>
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <?php if (!empty($widget['title'])): ?>
                        <div class="p-4 bg-gray-50 border-b">
                            <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($widget['title']); ?></h3>
                        </div>
                        <?php endif; ?>
                        <div class="aspect-video">
                            <iframe 
                                width="100%" 
                                height="100%" 
                                src="https://www.youtube.com/embed/<?php echo htmlspecialchars($widget['video_id']); ?>" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                    <?php 
                            endif;
                            break;
                            
                        case 'text':
                            if (!empty($widget['content'])):
                    ?>
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <?php if (!empty($widget['title'])): ?>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3"><?php echo htmlspecialchars($widget['title']); ?></h3>
                        <?php endif; ?>
                        <div class="text-gray-600 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($widget['content'])); ?>
                        </div>
                    </div>
                    <?php 
                            endif;
                            break;
                            
                        case 'location':
                            if (!empty($widget['address'])):
                    ?>
                    <div class="bg-white border-2 border-orange-500 rounded-xl p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <?php if (!empty($widget['title'])): ?>
                                <h3 class="text-lg font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($widget['title']); ?></h3>
                                <?php endif; ?>
                                <p class="text-gray-600"><?php echo htmlspecialchars($widget['address']); ?></p>
                            </div>
                        </div>
                        <?php if (!empty($widget['maps_url'])): ?>
                        <a href="<?php echo htmlspecialchars($widget['maps_url']); ?>" 
                           target="_blank"
                           class="block w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-6 rounded-lg text-center font-semibold transition">
                            <i class="fas fa-directions mr-2"></i>Ver no Mapa
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php 
                            endif;
                            break;
                    endswitch;
                    ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Informações de Contato -->
            <?php if (!empty($data['contact'])): ?>
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Contato</h3>
                <div class="space-y-3">
                    <?php if (!empty($data['contact']['email'])): ?>
                    <a href="mailto:<?php echo htmlspecialchars($data['contact']['email']); ?>" 
                       class="flex items-center justify-center gap-2 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo htmlspecialchars($data['contact']['email']); ?></span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($data['contact']['phone'])): ?>
                    <a href="tel:<?php echo htmlspecialchars($data['contact']['phone']); ?>" 
                       class="flex items-center justify-center gap-2 text-gray-600 hover:text-blue-600">
                        <i class="fas fa-phone"></i>
                        <span><?php echo htmlspecialchars($data['contact']['phone']); ?></span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($data['contact']['whatsapp'])): ?>
                    <a href="https://wa.me/<?php echo htmlspecialchars($data['contact']['whatsapp']); ?>" 
                       target="_blank"
                       class="flex items-center justify-center gap-2 text-gray-600 hover:text-green-600">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="text-center text-white text-sm opacity-75">
            <p>Criado com <i class="fas fa-heart text-red-400"></i> usando QR Code Generator</p>
        </div>
    </div>

    <script>
        // Função para copiar chave PIX
        function copyPix(key) {
            navigator.clipboard.writeText(key).then(() => {
                alert('Chave PIX copiada!');
            }).catch(() => {
                // Fallback
                const textarea = document.createElement('textarea');
                textarea.value = key;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('Chave PIX copiada!');
            });
        }

        // Função para abrir imagem em lightbox
        function openImage(src) {
            const lightbox = document.createElement('div');
            lightbox.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;display:flex;align-items:center;justify-content:center;cursor:pointer;';
            lightbox.onclick = () => lightbox.remove();
            
            const img = document.createElement('img');
            img.src = src;
            img.style.cssText = 'max-width:90%;max-height:90%;object-fit:contain;';
            
            lightbox.appendChild(img);
            document.body.appendChild(lightbox);
        }
    </script>
</body>
</html>
