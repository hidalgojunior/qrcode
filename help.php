<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajuda - Gerador de QR Code</title>
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
                <i class="fas fa-question-circle mr-3"></i>Ajuda e Documentação
            </h1>
            <p class="text-xl opacity-90">Aprenda como usar o Gerador de QR Code</p>
        </header>

        <main class="flex-1">
            <div class="bg-white rounded-2xl shadow-2xl p-8 backdrop-blur-sm max-w-6xl mx-auto">
                <div class="space-y-12">
                    <div class="space-y-6">
                        <h3 class="text-2xl font-bold text-primary flex items-center gap-3">
                            <i class="fas fa-info-circle"></i>O que são QR Codes?
                        </h3>
                        <p class="text-gray-700 leading-relaxed">QR Code (Quick Response Code) é um tipo de código de barras bidimensional que pode armazenar diversos tipos de informação, como texto, URLs, contatos, informações de WiFi e muito mais. Eles podem ser lidos rapidamente por smartphones e outros dispositivos com câmera.</p>
                    </div>

                    <div class="space-y-8">
                        <h3 class="text-2xl font-bold text-primary flex items-center gap-3">
                            <i class="fas fa-list"></i>Tipos de QR Code Disponíveis
                        </h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <h4 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-font text-blue-500"></i>Texto
                                </h4>
                                <p class="text-gray-600">Cria um QR Code com texto simples. Ideal para mensagens, notas ou qualquer texto que você queira compartilhar.</p>
                                <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-blue-500 font-mono text-sm">
                                    Exemplo: "Olá! Esta é uma mensagem de texto."
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-link text-green-500"></i>URL/Link
                                </h4>
                                <p class="text-gray-600">Gera um QR Code que, quando escaneado, abre uma página web específica.</p>
                                <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-green-500 font-mono text-sm">
                                    Exemplo: https://www.exemplo.com
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                                    <strong class="text-blue-700">Dica:</strong> Se você não incluir "http://" ou "https://", será adicionado automaticamente.
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-envelope text-red-500"></i>Email
                                </h4>
                                <p class="text-gray-600">Cria um QR Code que abre o aplicativo de email com destinatário, assunto e mensagem pré-preenchidos.</p>
                                <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-red-500 font-mono text-sm">
                                    Email: joao@exemplo.com<br>
                                    Assunto: Olá!<br>
                                    Mensagem: Como você está?
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-phone text-purple-500"></i>Telefone
                                </h4>
                                <p class="text-gray-600">Gera um QR Code que, quando escaneado, inicia uma chamada telefônica.</p>
                                <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-purple-500 font-mono text-sm">
                                    Exemplo: +55 11 99999-9999
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-sms text-yellow-500"></i>SMS
                                </h4>
                                <p class="text-gray-600">Cria um QR Code para enviar uma mensagem SMS com número e texto pré-definidos.</p>
                                <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-yellow-500 font-mono text-sm">
                                    Número: +55 11 99999-9999<br>
                                    Mensagem: Oi! Como você está?
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-wifi text-indigo-500"></i>WiFi
                                </h4>
                                <p class="text-gray-600">Gera um QR Code para conectar automaticamente a uma rede WiFi.</p>
                                <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-indigo-500 font-mono text-sm">
                                    Nome da Rede: MinhaRede<br>
                                    Senha: minhasenha123<br>
                                    Segurança: WPA/WPA2
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                                    <strong class="text-blue-700">Dica:</strong> Muito útil para compartilhar a senha do WiFi com visitantes!
                                </div>
                            </div>

                            <div class="space-y-4 lg:col-span-2">
                                <h4 class="text-xl font-semibold text-gray-700 flex items-center gap-2">
                                    <i class="fas fa-id-card text-pink-500"></i>Cartão de Visita (vCard)
                                </h4>
                                <p class="text-gray-600">Cria um QR Code com informações de contato que podem ser salvas diretamente na agenda do celular.</p>
                                <div class="bg-gray-100 p-4 rounded-lg border-l-4 border-pink-500 font-mono text-sm">
                                    Nome: João Silva<br>
                                    Empresa: Minha Empresa Ltda<br>
                                    Cargo: Desenvolvedor<br>
                                    Telefone: +55 11 99999-9999<br>
                                    Email: joao@exemplo.com<br>
                                    Website: https://meusite.com<br>
                                    <span class="text-pink-600">Instagram: @joaosilva</span>
                                </div>
                                <div class="bg-pink-50 border border-pink-200 rounded-lg p-4">
                                    <i class="fab fa-instagram text-pink-500 mr-2"></i>
                                    <strong class="text-pink-700">Novo:</strong> Agora você pode incluir seu Instagram no cartão de visita!
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-2xl font-bold text-primary flex items-center gap-3">
                            <i class="fas fa-cog"></i>Configurações Avançadas
                        </h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-xl font-semibold text-gray-700 mb-4">Tamanho do QR Code</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-square text-blue-500 w-5"></i>
                                        <div>
                                            <strong>Pequeno (200x200):</strong> Ideal para uso em documentos ou emails
                                        </div>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-square text-green-500 w-5"></i>
                                        <div>
                                            <strong>Médio (300x300):</strong> Tamanho padrão, bom para impressão
                                        </div>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-square text-yellow-500 w-5"></i>
                                        <div>
                                            <strong>Grande (400x400):</strong> Melhor visibilidade, ideal para cartazes
                                        </div>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-square text-red-500 w-5"></i>
                                        <div>
                                            <strong>Extra Grande (500x500):</strong> Máxima qualidade para impressões grandes
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h4 class="text-xl font-semibold text-gray-700 mb-4">Nível de Correção de Erro</h4>
                                <p class="text-gray-600 mb-4">Determina quanta informação redundante é adicionada ao QR Code para permitir recuperação em caso de danos:</p>
                                <ul class="space-y-3">
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-shield-alt text-red-400 w-5"></i>
                                        <div>
                                            <strong>Baixo (~7%):</strong> QR Code menor, menos resistente a danos
                                        </div>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-shield-alt text-yellow-400 w-5"></i>
                                        <div>
                                            <strong>Médio (~15%):</strong> Equilíbrio entre tamanho e resistência
                                        </div>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-shield-alt text-blue-500 w-5"></i>
                                        <div>
                                            <strong>Alto (~25%):</strong> Mais resistente a danos e sujeira
                                        </div>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-shield-alt text-green-500 w-5"></i>
                                        <div>
                                            <strong>Muito Alto (~30%):</strong> Máxima resistência, QR Code maior
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="index.php" class="bg-gradient-to-r from-primary to-secondary hover:from-secondary hover:to-primary text-white font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-xl inline-flex items-center gap-3">
                            <i class="fas fa-arrow-left"></i>Voltar ao Gerador
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <footer class="text-center mt-10 text-white opacity-80">
            <p>&copy; 2025 Gerador de QR Code. Desenvolvido com <i class="fas fa-heart text-red-300"></i></p>
        </footer>
    </div>
</body>
</html>