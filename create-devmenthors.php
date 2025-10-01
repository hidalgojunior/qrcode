<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar DevMenthors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .theme-transition { transition: all 0.3s ease; }
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
        /* Scrollbar customizada */
        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        /* Line clamp */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen theme-transition">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-4 mb-4">
                <a href="index.php" class="text-white hover:text-blue-300">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
                <h1 class="text-4xl font-bold text-white">
                    <i class="fas fa-id-card mr-2"></i>Criar DevMenthors
                </h1>
                <button id="themeToggle" class="p-3 rounded-full bg-white/20 hover:bg-white/30 text-white transition duration-300 backdrop-blur-lg border-2 border-white/30 shadow-lg">
                    <i class="fas fa-moon text-xl"></i>
                </button>
            </div>
            <p class="text-white/90 text-lg">Crie sua página personalizada com QR Code</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Editor -->
            <div class="bg-white/15 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border-2 border-white/30">
                <h2 class="text-2xl font-bold text-white mb-6">
                    <i class="fas fa-edit mr-2 text-yellow-300"></i>Editor
                </h2>

                <form id="micrositeForm" class="space-y-6">
                    <!-- Informações Básicas -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white border-b-2 border-yellow-400/50 pb-2">
                            Informações Básicas
                        </h3>
                        
                        <div>
                            <label class="block text-white text-sm font-bold mb-2">
                                URL Personalizada * 
                                <span class="text-xs font-normal text-yellow-200">(seu-nome-ou-marca)</span>
                            </label>
                            <div class="mb-2">
                                <div class="bg-gradient-to-r from-green-blue/30 to-picton-blue/30 border-2 border-verdigris/50 rounded-xl p-3 backdrop-blur-sm" style="background: linear-gradient(90deg, rgba(35,100,170,0.3), rgba(61,165,217,0.3));">
                                    <p class="text-xs text-yellow-200 mb-1">Sua URL será:</p>
                                    <p class="text-white font-mono text-sm break-all">
                                        <span class="text-yellow-300">http://seusite.com/</span>devmenthors.php?id=<span id="slugPreview" class="text-green-300 font-bold">seu-slug</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" id="slug" required
                                       class="flex-1 px-4 py-3 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl focus:outline-none focus:border-yellow-400 placeholder-white/50 backdrop-blur-sm"
                                       placeholder="meunome"
                                       pattern="[a-z0-9-]+"
                                       maxlength="50">
                            </div>
                            <p class="text-xs text-yellow-200 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Apenas letras minúsculas, números e hífen (-)
                            </p>
                            <p class="text-xs text-orange-300 mt-1">
                                <i class="fas fa-star mr-1"></i>Exemplos: joao-silva, minhaempresa, chef-mario
                            </p>
                            <div id="slugStatus" class="text-sm mt-2 font-semibold"></div>
                        </div>
                        
                        <div>
                            <label class="block text-white text-sm font-bold mb-2">Nome *</label>
                            <input type="text" id="name" required
                                   class="w-full px-4 py-3 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl focus:outline-none focus:border-yellow-400 placeholder-white/50 backdrop-blur-sm"
                                   placeholder="Seu nome ou nome da empresa">
                        </div>

                        <div>
                            <label class="block text-white text-sm font-bold mb-2">Título/Cargo</label>
                            <input type="text" id="title"
                                   class="w-full px-4 py-3 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl focus:outline-none focus:border-yellow-400 placeholder-white/50 backdrop-blur-sm"
                                   placeholder="Ex: Desenvolvedor Web">
                        </div>

                        <div>
                            <label class="block text-white text-sm font-bold mb-2">Bio</label>
                            <textarea id="bio" rows="3"
                                      class="w-full px-4 py-3 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl focus:outline-none focus:border-yellow-400 placeholder-white/50 backdrop-blur-sm"
                                      placeholder="Conte um pouco sobre você..."></textarea>
                        </div>

                        <div>
                            <label class="block text-white text-sm font-bold mb-2">
                                <i class="fas fa-image mr-2"></i>Foto/Avatar
                            </label>
                            
                            <!-- Preview do Avatar -->
                            <div id="avatarPreview" class="mb-3 hidden">
                                <div class="relative inline-block">
                                    <img id="avatarPreviewImg" src="" alt="Preview" class="w-32 h-32 rounded-full object-cover border-4 border-yellow-400 shadow-lg">
                                    <button type="button" onclick="removeAvatar()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 shadow-lg">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Upload de arquivo -->
                            <div class="mb-2">
                                <label class="block w-full cursor-pointer">
                                    <div class="border-2 border-dashed border-verdigris/50 rounded-xl p-4 text-center hover:border-yellow-400 transition bg-white/5 backdrop-blur-sm">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-yellow-300 mb-2"></i>
                                        <p class="text-white text-sm mb-1">
                                            <span class="font-bold">Clique para fazer upload</span>
                                        </p>
                                        <p class="text-white/60 text-xs">ou arraste e solte aqui</p>
                                        <p class="text-white/40 text-xs mt-1">PNG, JPG até 2MB</p>
                                    </div>
                                    <input type="file" id="avatarUpload" accept="image/*" class="hidden">
                                </label>
                            </div>
                            
                            <!-- OU URL -->
                            <div class="text-center text-white/60 text-xs my-2">— OU —</div>
                            
                            <input type="url" id="avatar"
                                   class="w-full px-4 py-3 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl focus:outline-none focus:border-yellow-400 placeholder-white/50 backdrop-blur-sm"
                                   placeholder="Cole a URL de uma imagem">
                        </div>
                    </div>

                    <!-- Tema -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white border-b-2 border-yellow-400/50 pb-2">
                            Tema de Cores
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-white text-sm font-bold mb-2">Cor Inicial</label>
                                <input type="color" id="gradient_start" value="#3da5d9"
                                       class="w-full h-12 rounded-xl border-2 border-verdigris/50 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-white text-sm font-bold mb-2">Cor Final</label>
                                <input type="color" id="gradient_end" value="#73bfb8"
                                       class="w-full h-12 rounded-xl border-2 border-verdigris/50 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Redes Sociais -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white border-b-2 border-yellow-400/50 pb-2">
                            Redes Sociais
                        </h3>
                        
                        <div id="socialInputs" class="space-y-3">
                            <!-- Instagram -->
                            <div class="flex gap-2">
                                <input type="url" placeholder="URL do Instagram"
                                       class="flex-1 px-4 py-2 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                       data-social="instagram" data-icon="fab fa-instagram">
                                <span class="flex items-center justify-center w-12 h-12 bg-gray-800 text-white rounded-lg border border-blue-500/50">
                                    <i class="fab fa-instagram"></i>
                                </span>
                            </div>

                            <!-- Facebook -->
                            <div class="flex gap-2">
                                <input type="url" placeholder="URL do Facebook"
                                       class="flex-1 px-4 py-2 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                       data-social="facebook" data-icon="fab fa-facebook">
                                <span class="flex items-center justify-center w-12 h-12 bg-gray-800 text-white rounded-lg border border-blue-500/50">
                                    <i class="fab fa-facebook"></i>
                                </span>
                            </div>

                            <!-- LinkedIn -->
                            <div class="flex gap-2">
                                <input type="url" placeholder="URL do LinkedIn"
                                       class="flex-1 px-4 py-2 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                       data-social="linkedin" data-icon="fab fa-linkedin">
                                <span class="flex items-center justify-center w-12 h-12 bg-gray-800 text-white rounded-lg border border-blue-500/50">
                                    <i class="fab fa-linkedin"></i>
                                </span>
                            </div>

                            <!-- Twitter/X -->
                            <div class="flex gap-2">
                                <input type="url" placeholder="URL do Twitter/X"
                                       class="flex-1 px-4 py-2 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                       data-social="twitter" data-icon="fab fa-twitter">
                                <span class="flex items-center justify-center w-12 h-12 bg-gray-800 text-white rounded-lg border border-blue-500/50">
                                    <i class="fab fa-twitter"></i>
                                </span>
                            </div>

                            <!-- YouTube -->
                            <div class="flex gap-2">
                                <input type="url" placeholder="URL do YouTube"
                                       class="flex-1 px-4 py-2 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                       data-social="youtube" data-icon="fab fa-youtube">
                                <span class="flex items-center justify-center w-12 h-12 bg-gray-800 text-white rounded-lg border border-blue-500/50">
                                    <i class="fab fa-youtube"></i>
                                </span>
                            </div>

                            <!-- TikTok -->
                            <div class="flex gap-2">
                                <input type="url" placeholder="URL do TikTok"
                                       class="flex-1 px-4 py-2 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                       data-social="tiktok" data-icon="fab fa-tiktok">
                                <span class="flex items-center justify-center w-12 h-12 bg-gray-800 text-white rounded-lg border border-blue-500/50">
                                    <i class="fab fa-tiktok"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Widgets/Componentes -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b-2 border-yellow-400/50 pb-2">
                            <h3 class="text-lg font-semibold text-white">Widgets & Componentes</h3>
                            <div class="relative">
                                <button type="button" id="addWidgetBtn" class="px-4 py-2 gradient-accent hover:opacity-90 text-white rounded-xl text-sm font-semibold shadow-lg">
                                    <i class="fas fa-plus mr-1"></i>Adicionar Widget
                                </button>
                                <div id="widgetMenu" class="hidden absolute right-0 mt-2 w-64 bg-white/10 backdrop-blur-xl rounded-xl shadow-2xl border-2 border-white/30 z-10">
                                    <button type="button" onclick="addWidget('link')" class="w-full text-left px-4 py-3 hover:bg-white/20 text-white rounded-t-xl transition">
                                        <i class="fas fa-link mr-2 text-picton-blue"></i>Link/Botão
                                    </button>
                                    <button type="button" onclick="addWidget('pix')" class="w-full text-left px-4 py-3 hover:bg-white/20 text-white transition">
                                        <i class="fas fa-money-bill mr-2 text-green-400"></i>Chave PIX
                                    </button>
                                    <button type="button" onclick="addWidget('gallery')" class="w-full text-left px-4 py-3 hover:bg-white/20 text-white transition">
                                        <i class="fas fa-images mr-2 text-purple-400"></i>Galeria de Fotos
                                    </button>
                                    <button type="button" onclick="addWidget('music')" class="w-full text-left px-4 py-3 hover:bg-white/20 text-white transition">
                                        <i class="fas fa-music mr-2 text-pink-400"></i>Player de Música
                                    </button>
                                    <button type="button" onclick="addWidget('video')" class="w-full text-left px-4 py-3 hover:bg-white/20 text-white transition">
                                        <i class="fas fa-video mr-2 text-red-400"></i>Vídeo (YouTube)
                                    </button>
                                    <button type="button" onclick="addWidget('text')" class="w-full text-left px-4 py-3 hover:bg-white/20 text-white transition">
                                        <i class="fas fa-align-left mr-2 text-yellow-400"></i>Texto/HTML
                                    </button>
                                    <button type="button" onclick="addWidget('location')" class="w-full text-left px-4 py-3 hover:bg-white/20 text-white rounded-b-xl transition">
                                        <i class="fas fa-map-marker-alt mr-2 text-orange-400"></i>Localização
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="widgetsContainer" class="space-y-3">
                            <!-- Widgets serão adicionados aqui -->
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white border-b-2 border-yellow-400/50 pb-2">
                            Informações de Contato
                        </h3>
                        
                        <div>
                            <label class="block text-white text-sm font-bold mb-2">Email</label>
                            <input type="email" id="contact_email"
                                   class="w-full px-4 py-3 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                   placeholder="seu@email.com">
                        </div>

                        <div>
                            <label class="block text-white text-sm font-bold mb-2">Telefone</label>
                            <input type="tel" id="contact_phone"
                                   class="w-full px-4 py-3 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                   placeholder="(XX) XXXXX-XXXX">
                        </div>

                        <div>
                            <label class="block text-white text-sm font-bold mb-2">WhatsApp (com código do país)</label>
                            <input type="tel" id="contact_whatsapp"
                                   class="w-full px-4 py-3 bg-white/10 text-white border-2 border-verdigris/50 rounded-xl backdrop-blur-sm focus:outline-none focus:border-yellow-400"
                                   placeholder="5511999999999">
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-4">
                        <button type="button" id="preview" 
                                class="flex-1 bg-gradient-to-r from-verdigris to-picton-blue hover:opacity-90 text-white font-bold py-4 px-6 rounded-xl transition shadow-lg transform hover:scale-105" style="background: linear-gradient(90deg, #73bfb8, #3da5d9);">
                            <i class="fas fa-eye mr-2"></i>Visualizar
                        </button>
                        <button type="submit" 
                                class="flex-1 gradient-accent hover:opacity-90 text-white font-bold py-4 px-6 rounded-xl transition shadow-lg transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i>Criar DevMenthors
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview -->
            <div class="space-y-6">
                <!-- Preview Mobile -->
                <div class="bg-white/15 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border-2 border-white/30">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-white">
                            <i class="fas fa-mobile-alt mr-2 text-yellow-300"></i>Preview Mobile
                        </h2>
                        <span class="text-white/60 text-sm">
                            <i class="fas fa-info-circle mr-1"></i>375 x 667px
                        </span>
                    </div>
                    
                    <!-- Moldura de Celular -->
                    <div class="relative mx-auto" style="width: 375px; max-width: 100%;">
                        <!-- Notch do iPhone -->
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-40 h-6 bg-black rounded-b-3xl z-10"></div>
                        
                        <!-- Tela -->
                        <div class="bg-black rounded-[3rem] p-3 shadow-2xl border-8 border-gray-900">
                            <div class="bg-gradient-to-br from-picton-blue/20 to-verdigris/20 rounded-[2.5rem] overflow-hidden" style="height: 667px;">
                                <div class="h-full overflow-y-auto scrollbar-thin scrollbar-thumb-white/20">
                                    <div id="previewContentMobile" class="bg-white p-6">
                                        <p class="text-gray-400 text-center text-sm">Preencha os campos para ver o preview...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botão Home -->
                        <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 w-32 h-1 bg-white/20 rounded-full"></div>
                    </div>
                </div>

                <!-- Preview Desktop -->
                <div class="bg-white/15 backdrop-blur-xl rounded-2xl shadow-2xl p-6 border-2 border-white/30">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-white">
                            <i class="fas fa-desktop mr-2 text-yellow-300"></i>Preview Desktop
                        </h2>
                        <span class="text-white/60 text-sm">
                            <i class="fas fa-info-circle mr-1"></i>1920 x 1080px
                        </span>
                    </div>
                    
                    <!-- Moldura de Desktop/Browser -->
                    <div class="bg-gray-900 rounded-xl shadow-2xl overflow-hidden">
                        <!-- Barra do Browser -->
                        <div class="bg-gray-800 px-4 py-2 flex items-center gap-2 border-b border-gray-700">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <div class="flex-1 bg-gray-700 rounded-lg px-3 py-1 mx-4">
                                <p class="text-white/60 text-xs truncate">
                                    <i class="fas fa-lock mr-1"></i>devmenthors.php?id=<span id="slugPreviewDesktop">seu-slug</span>
                                </p>
                            </div>
                            <div class="flex gap-2 text-white/40">
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                        </div>
                        
                        <!-- Conteúdo do Desktop -->
                        <div class="bg-gradient-to-br from-picton-blue/20 to-verdigris/20" style="height: 500px; overflow-y: auto;">
                            <div class="max-w-4xl mx-auto py-8 px-4">
                                <div id="previewContentDesktop" class="bg-white rounded-2xl p-8 shadow-2xl">
                                    <p class="text-gray-400 text-center">Preencha os campos para ver o preview...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/devmenthors-editor.js"></script>
</body>
</html>
