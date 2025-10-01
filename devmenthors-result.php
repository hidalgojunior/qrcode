<?php
require_once 'config.php';

$micrositeId = $_GET['id'] ?? null;

if (!$micrositeId) {
    header('Location: create-devmenthors.php');
    exit;
}

$micrositeUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/devmenthors.php?id=' . $micrositeId;
$qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($micrositeUrl);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevMenthors Criado!</title>
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
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue), var(--verdigris));
        }
        .gradient-accent {
            background: linear-gradient(90deg, var(--mikado-yellow), var(--pumpkin));
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Sucesso -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 gradient-accent rounded-full mb-4 shadow-2xl">
                    <i class="fas fa-check text-5xl text-white"></i>
                </div>
                <h1 class="text-5xl font-bold text-white mb-2">DevMenthors Criado com Sucesso!</h1>
                <p class="text-white/90 text-lg">Sua página está pronta para ser compartilhada</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- QR Code -->
                <div class="bg-white/15 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border-2 border-white/30">
                    <h2 class="text-2xl font-bold text-white mb-4 text-center">
                        <i class="fas fa-qrcode mr-2 text-yellow-300"></i>QR Code
                    </h2>
                    
                    <div class="bg-white rounded-xl p-6 mb-4">
                        <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code" class="w-full max-w-sm mx-auto">
                    </div>

                    <div class="space-y-3">
                        <a href="<?php echo $qrCodeUrl; ?>&download=1" download="qrcode-devmenthors.png"
                           class="block w-full gradient-accent hover:opacity-90 text-white font-bold py-4 px-6 rounded-xl text-center transition shadow-lg transform hover:scale-105">
                            <i class="fas fa-download mr-2"></i>Baixar QR Code
                        </a>
                    </div>
                </div>

                <!-- Informações -->
                <div class="bg-white/15 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border-2 border-white/30">
                    <h2 class="text-2xl font-bold text-white mb-4">
                        <i class="fas fa-link mr-2 text-yellow-300"></i>Compartilhe
                    </h2>

                    <div class="space-y-4">
                        <!-- URL -->
                        <div>
                            <label class="block text-white text-sm font-bold mb-2">URL da Sua Página</label>
                            <div class="flex gap-2">
                                <input type="text" id="micrositeUrl" value="<?php echo $micrositeUrl; ?>" readonly
                                       class="flex-1 px-4 py-2 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm">
                                <button onclick="copyUrl()" 
                                        class="px-4 py-2 bg-picton-blue hover:opacity-90 text-white rounded-xl transition shadow-lg" style="background-color: var(--picton-blue);">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Botões de ação -->
                        <a href="<?php echo $micrositeUrl; ?>" target="_blank"
                           class="block w-full bg-verdigris hover:opacity-90 text-white font-bold py-4 px-6 rounded-xl text-center transition shadow-lg transform hover:scale-105" style="background-color: var(--verdigris);">
                            <i class="fas fa-eye mr-2"></i>Ver Página
                        </a>

                        <a href="create-devmenthors.php"
                           class="block w-full bg-picton-blue hover:opacity-90 text-white font-bold py-4 px-6 rounded-xl text-center transition shadow-lg transform hover:scale-105" style="background-color: var(--picton-blue);">
                            <i class="fas fa-plus mr-2"></i>Criar Nova Página
                        </a>

                        <a href="index.php"
                           class="block w-full bg-green-blue hover:opacity-90 text-white font-bold py-4 px-6 rounded-xl text-center transition shadow-lg transform hover:scale-105" style="background-color: var(--green-blue);">
                            <i class="fas fa-home mr-2"></i>Voltar ao Início
                        </a>

                        <!-- Compartilhar nas redes -->
                        <div class="pt-4 border-t border-blue-500/30">
                            <p class="text-white text-sm font-bold mb-3">Compartilhar em:</p>
                            <div class="flex gap-2">
                                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode('Confira meu perfil: ' . $micrositeUrl); ?>" 
                                   target="_blank"
                                   class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg text-center transition">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($micrositeUrl); ?>" 
                                   target="_blank"
                                   class="flex-1 bg-blue-700 hover:bg-blue-800 text-white py-2 px-4 rounded-lg text-center transition">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($micrositeUrl); ?>&text=<?php echo urlencode('Confira meu perfil!'); ?>" 
                                   target="_blank"
                                   class="flex-1 bg-sky-500 hover:bg-sky-600 text-white py-2 px-4 rounded-lg text-center transition">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://t.me/share/url?url=<?php echo urlencode($micrositeUrl); ?>&text=<?php echo urlencode('Confira meu perfil!'); ?>" 
                                   target="_blank"
                                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-center transition">
                                    <i class="fab fa-telegram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dicas -->
            <div class="mt-6 bg-white/15 backdrop-blur-xl rounded-2xl p-6 border-2 border-yellow-400/50">
                <h3 class="text-2xl font-bold text-white mb-4">
                    <i class="fas fa-lightbulb mr-2 text-yellow-300"></i>Dicas de Uso
                </h3>
                <ul class="text-white space-y-3">
                    <li class="flex items-start"><i class="fas fa-check text-green-400 mr-3 mt-1"></i><span>Imprima o QR Code e coloque em cartões de visita</span></li>
                    <li class="flex items-start"><i class="fas fa-check text-green-400 mr-3 mt-1"></i><span>Adicione o QR Code em suas redes sociais</span></li>
                    <li class="flex items-start"><i class="fas fa-check text-green-400 mr-3 mt-1"></i><span>Use em apresentações e materiais de marketing</span></li>
                    <li class="flex items-start"><i class="fas fa-check text-green-400 mr-3 mt-1"></i><span>Compartilhe o link direto da sua página personalizada</span></li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function copyUrl() {
            const input = document.getElementById('micrositeUrl');
            input.select();
            document.execCommand('copy');
            alert('URL copiada para a área de transferência!');
        }
    </script>
</body>
</html>
