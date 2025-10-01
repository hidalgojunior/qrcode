<?php
session_start();

// Verificar se existe um QR code gerado
if (!isset($_SESSION['qr_generated']) || !isset($_SESSION['qr_result'])) {
    header('Location: index.php');
    exit;
}

$qrResult = $_SESSION['qr_result'];
$qrInfo = $_SESSION['qr_info'];

// Limpar sessão após exibir (opcional - pode comentar se quiser manter o histórico)
// unset($_SESSION['qr_generated'], $_SESSION['qr_result'], $_SESSION['qr_info']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Gerado - Gerador de QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#667eea',
                        secondary: '#764ba2'
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-primary to-secondary">
    <div class="container mx-auto px-4 py-8 min-h-screen flex flex-col">
        <header class="text-center mb-10 text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 drop-shadow-lg">
                <i class="fas fa-qrcode mr-3"></i>QR Code Gerado com Sucesso!
            </h1>
            <p class="text-xl opacity-90">Seu QR code está pronto para uso</p>
        </header>

        <main class="flex-1">
            <div class="bg-white rounded-2xl shadow-2xl p-8 backdrop-blur-sm max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>QR Code Criado
                </h2>
                
                <div class="text-center mb-8">
                    <div class="inline-block bg-gray-50 p-6 rounded-2xl shadow-inner">
                        <img src="<?php echo htmlspecialchars($qrResult['url']); ?>" 
                             alt="QR Code Gerado" 
                             id="qr-image"
                             class="mx-auto max-w-full h-auto rounded-lg shadow-md">
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                    <h3 class="text-xl font-semibold text-blue-800 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Informações do QR Code
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-blue-700"><strong>Tipo:</strong> <?php echo htmlspecialchars($qrInfo['type_name']); ?></p>
                            <p class="text-sm text-blue-700"><strong>Tamanho:</strong> <?php echo htmlspecialchars($qrInfo['size']); ?> pixels</p>
                        </div>
                        <div>
                            <p class="text-sm text-blue-700"><strong>Correção de Erro:</strong> <?php echo htmlspecialchars($qrInfo['error_correction']); ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-blue-700 mb-2"><strong>Conteúdo:</strong></p>
                        <div class="bg-white p-4 rounded-lg border border-blue-200 text-sm font-mono text-gray-700 break-all max-h-32 overflow-y-auto">
                            <?php echo nl2br(htmlspecialchars($qrInfo['data'])); ?>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3 justify-center mb-8">
                    <a href="download.php?file=<?php echo urlencode($qrResult['filename']); ?>&type=png" 
                       class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
                        <i class="fas fa-download"></i> Baixar PNG
                    </a>
                    <a href="download.php?file=<?php echo urlencode($qrResult['filename']); ?>&type=jpg" 
                       class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
                        <i class="fas fa-download"></i> Baixar JPG
                    </a>
                    <button onclick="printQR()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <button onclick="shareQR()" 
                            class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
                        <i class="fas fa-share-alt"></i> Compartilhar
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-lg inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Gerar Novo QR Code
                    </a>
                </div>
            </div>
        </main>

        <footer class="text-center mt-10 text-white opacity-80">
            <p>&copy; 2025 Gerador de QR Code. Desenvolvido com <i class="fas fa-heart text-red-300"></i></p>
        </footer>
    </div>

    <!-- Modal para compartilhamento -->
    <div id="shareModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
            <h3 class="text-2xl font-bold text-gray-800 text-center mb-6">
                <i class="fas fa-share-alt text-purple-500 mr-2"></i>Compartilhar QR Code
            </h3>
            <div class="space-y-3 mb-6">
                <button onclick="copyToClipboard()" 
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-lg font-medium transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-copy"></i> Copiar Link
                </button>
                <button onclick="shareWhatsApp()" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-medium transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </button>
                <button onclick="shareEmail()" 
                        class="w-full bg-red-500 hover:bg-red-600 text-white py-3 px-4 rounded-lg font-medium transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-envelope"></i> Email
                </button>
            </div>
            <button onclick="closeModal()" 
                    class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition-all duration-300">
                Fechar
            </button>
        </div>
    </div>

    <script>
        function printQR() {
            const qrImage = document.getElementById('qr-image');
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Imprimir QR Code</title>
                    <style>
                        body { 
                            margin: 0; 
                            padding: 20px; 
                            text-align: center; 
                            font-family: Arial, sans-serif; 
                        }
                        img { 
                            max-width: 100%; 
                            height: auto; 
                        }
                        .info {
                            margin-top: 20px;
                            font-size: 14px;
                            color: #666;
                        }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    <h2>QR Code - <?php echo htmlspecialchars($qrInfo['type_name']); ?></h2>
                    <img src="${qrImage.src}" alt="QR Code">
                    <div class="info">
                        <p>Gerado em: ${new Date().toLocaleDateString('pt-BR')}</p>
                        <p>Tamanho: <?php echo htmlspecialchars($qrInfo['size']); ?></p>
                        <p>Tipo: <?php echo htmlspecialchars($qrInfo['type_name']); ?></p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

        function shareQR() {
            document.getElementById('shareModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('shareModal').classList.add('hidden');
        }

        function copyToClipboard() {
            const qrImage = document.getElementById('qr-image');
            const fullUrl = window.location.origin + '/' + qrImage.src;
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(fullUrl).then(() => {
                    alert('Link copiado para a área de transferência!');
                });
            } else {
                // Fallback para navegadores mais antigos
                const textarea = document.createElement('textarea');
                textarea.value = fullUrl;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('Link copiado para a área de transferência!');
            }
        }

        function shareWhatsApp() {
            const qrImage = document.getElementById('qr-image');
            const fullUrl = window.location.origin + '/' + qrImage.src;
            const message = `Confira este QR Code que eu criei: ${fullUrl}`;
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }

        function shareEmail() {
            const qrImage = document.getElementById('qr-image');
            const fullUrl = window.location.origin + '/' + qrImage.src;
            const subject = 'QR Code Gerado';
            const body = `Olá!%0A%0AConfira este QR Code que eu criei:%0A${fullUrl}%0A%0ATipo: <?php echo urlencode($qrInfo['type_name']); ?>%0ATamanho: <?php echo urlencode($qrInfo['size']); ?>`;
            const emailUrl = `mailto:?subject=${subject}&body=${body}`;
            window.location.href = emailUrl;
        }

        // Fechar modal ao clicar fora dele
        document.getElementById('shareModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Suporte para Web Share API (mobile)
        if (navigator.share) {
            function shareQR() {
                const qrImage = document.getElementById('qr-image');
                const fullUrl = window.location.origin + '/' + qrImage.src;
                
                navigator.share({
                    title: 'QR Code Gerado',
                    text: 'Confira este QR Code que eu criei!',
                    url: fullUrl
                }).catch(err => {
                    console.log('Erro ao compartilhar:', err);
                    document.getElementById('shareModal').classList.remove('hidden');
                });
            }
        }
    </script>
</body>
</html>