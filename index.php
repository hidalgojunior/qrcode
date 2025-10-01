<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .theme-transition {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
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
<body class="gradient-bg min-h-screen theme-transition">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-4 mb-4">
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-qrcode mr-2"></i>Gerador de QR Code
                </h1>
                
                <!-- Toggle de Tema -->
                <button id="themeToggle" class="p-3 rounded-full bg-white/20 hover:bg-white/30 text-white transition duration-300 backdrop-blur-lg border-2 border-white/30 shadow-lg">
                    <i class="fas fa-moon text-xl"></i>
                </button>
            </div>
            <p class="text-white/90 text-lg" id="subtitle">Crie QR Codes personalizados gratuitamente</p>
            
            <!-- Botão DevMenthors -->
            <div class="mt-4">
                <a href="create-devmenthors.php" 
                   class="inline-flex items-center gap-2 px-6 py-3 gradient-accent hover:opacity-90 text-white font-bold rounded-full transition duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-id-card"></i>
                    <span>Criar DevMenthors</span>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">NOVO</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal - Formulário -->
            <div class="lg:col-span-2">
                <div id="mainCard" class="bg-white/15 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border-2 border-white/30 theme-transition">
                    <form id="qrForm" method="POST" action="generate.php" enctype="multipart/form-data">
                        <!-- Seleção do Tipo de QR Code -->
                        <div class="mb-6">
                            <label class="block text-white text-sm font-bold mb-2" id="typeLabel">
                                <i class="fas fa-list-ul mr-2"></i>Tipo de QR Code
                            </label>
                            <select id="qr_type" name="qr_type" 
                                    class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 transition theme-transition">
                                <option value="text">📝 Texto</option>
                                <option value="url">🌐 URL / Link</option>
                                <option value="email">📧 Email</option>
                                <option value="phone">📱 Telefone</option>
                                <option value="sms">💬 SMS</option>
                                <option value="wifi">📶 WiFi</option>
                                <option value="vcard">👤 vCard (Contato)</option>
                            </select>
                        </div>

                        <!-- Formulário para Texto -->
                        <div id="text_form" class="form-type">
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Texto</label>
                                <textarea name="text" rows="4" 
                                          class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                          placeholder="Digite seu texto aqui..."></textarea>
                            </div>
                        </div>

                        <!-- Formulário para URL -->
                        <div id="url_form" class="form-type hidden">
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">URL / Link</label>
                                <input type="url" name="url" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="https://exemplo.com.br">
                            </div>
                        </div>

                        <!-- Formulário para Email -->
                        <div id="email_form" class="form-type hidden">
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Email</label>
                                <input type="email" name="email" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="exemplo@email.com">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Assunto (opcional)</label>
                                <input type="text" name="email_subject" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="Assunto do email">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Mensagem (opcional)</label>
                                <textarea name="email_body" rows="3" 
                                          class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                          placeholder="Mensagem do email"></textarea>
                            </div>
                        </div>

                        <!-- Formulário para Telefone -->
                        <div id="phone_form" class="form-type hidden">
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Número de Telefone</label>
                                <input type="text" name="phone" id="phone_input"
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="Digite apenas os números">
                                <p class="text-blue-200 text-xs mt-1">Formato: (XX) XXXXX-XXXX</p>
                            </div>
                        </div>

                        <!-- Formulário para SMS -->
                        <div id="sms_form" class="form-type hidden">
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Número de Telefone</label>
                                <input type="text" name="sms_phone" id="sms_phone_input"
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="Digite apenas os números">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Mensagem</label>
                                <textarea name="sms_message" rows="3" 
                                          class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                          placeholder="Digite a mensagem"></textarea>
                            </div>
                        </div>

                        <!-- Formulário para WiFi -->
                        <div id="wifi_form" class="form-type hidden">
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Nome da Rede (SSID)</label>
                                <input type="text" name="wifi_ssid" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="Nome da rede WiFi">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Senha</label>
                                <input type="text" name="wifi_password" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="Senha da rede">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Tipo de Segurança</label>
                                <select name="wifi_encryption" 
                                        class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition">
                                    <option value="WPA">WPA/WPA2</option>
                                    <option value="WEP">WEP</option>
                                    <option value="nopass">Sem senha</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="flex items-center text-white">
                                    <input type="checkbox" name="wifi_hidden" value="true" class="mr-2">
                                    <span>Rede oculta</span>
                                </label>
                            </div>
                        </div>

                        <!-- Formulário para vCard -->
                        <div id="vcard_form" class="form-type hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-white text-sm font-bold mb-2">Nome</label>
                                    <input type="text" name="vcard_firstname" 
                                           class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                           placeholder="Nome">
                                </div>
                                <div>
                                    <label class="block text-white text-sm font-bold mb-2">Sobrenome</label>
                                    <input type="text" name="vcard_lastname" 
                                           class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                           placeholder="Sobrenome">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Telefone</label>
                                <input type="text" name="vcard_phone" id="vcard_phone_input"
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="Digite apenas os números">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Email</label>
                                <input type="email" name="vcard_email" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="email@exemplo.com">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Empresa (opcional)</label>
                                <input type="text" name="vcard_company" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="Nome da empresa">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Cargo (opcional)</label>
                                <input type="text" name="vcard_title" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="Cargo">
                            </div>
                            <div class="mb-4">
                                <label class="block text-white text-sm font-bold mb-2">Instagram (opcional)</label>
                                <input type="text" name="vcard_instagram" 
                                       class="w-full px-4 py-3 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                       placeholder="@usuario">
                            </div>
                        </div>

                        <!-- Botão Gerar -->
                        <div class="mt-6">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-4 px-6 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-magic mr-2"></i>Gerar QR Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Coluna Lateral - Personalização -->
            <div class="lg:col-span-1">
                <div id="sideCard" class="bg-white/10 backdrop-blur-lg rounded-xl shadow-2xl p-6 border border-blue-500/30 sticky top-4 theme-transition">
                    <h2 class="text-xl font-bold text-white mb-4">
                        <i class="fas fa-palette mr-2"></i>Personalização
                    </h2>

                    <!-- Tamanho -->
                    <div class="mb-6">
                        <label class="block text-white text-sm font-bold mb-2">Tamanho</label>
                        <select name="size" form="qrForm" 
                                class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition">
                            <option value="200">Pequeno (200x200)</option>
                            <option value="300" selected>Médio (300x300)</option>
                            <option value="500">Grande (500x500)</option>
                            <option value="800">Extra Grande (800x800)</option>
                        </select>
                    </div>

                    <!-- Cores -->
                    <div class="mb-6">
                        <label class="block text-white text-sm font-bold mb-2">Cor do QR Code</label>
                        <div class="flex gap-2">
                            <input type="color" id="fg_color" value="#000000" 
                                   class="w-16 h-10 rounded border border-blue-500/50 cursor-pointer">
                            <input type="text" id="fg_color_text" name="fg_color" form="qrForm" value="#000000" 
                                   class="flex-1 px-3 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                   placeholder="#000000">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-white text-sm font-bold mb-2">Cor de Fundo</label>
                        <div class="flex gap-2">
                            <input type="color" id="bg_color" value="#FFFFFF" 
                                   class="w-16 h-10 rounded border border-blue-500/50 cursor-pointer">
                            <input type="text" id="bg_color_text" name="bg_color" form="qrForm" value="#FFFFFF" 
                                   class="flex-1 px-3 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 theme-transition"
                                   placeholder="#FFFFFF">
                        </div>
                    </div>

                    <!-- Margem -->
                    <div class="mb-6">
                        <label class="block text-white text-sm font-bold mb-2">Margem: <span id="margin_value">4</span>px</label>
                        <input type="range" id="margin" name="margin" form="qrForm" min="0" max="10" value="4" 
                               class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer">
                    </div>

                    <!-- Logo/Imagem -->
                    <div class="mb-4">
                        <label class="block text-white text-sm font-bold mb-2">
                            <i class="fas fa-image mr-2"></i>Logo/Imagem (opcional)
                        </label>
                        <input type="file" name="logo" form="qrForm" accept="image/*" 
                               class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-600 file:text-white file:cursor-pointer hover:file:bg-blue-700 theme-transition">
                        <p class="text-blue-200 text-xs mt-1">Formatos: JPG, PNG, GIF (max 2MB)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
