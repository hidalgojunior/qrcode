<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevMenthors - Crie seu Microsite e QR Codes Personalizados</title>
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

        .gradient-text {
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue));
        }

        .gradient-card {
            background: linear-gradient(135deg, rgba(35, 100, 170, 0.1), rgba(61, 165, 217, 0.1));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(61, 165, 217, 0.3);
        }

        .hero-gradient {
            background: linear-gradient(135deg, #2364aa 0%, #3da5d9 50%, #73bfb8 100%);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float-delay {
            animation: float 6s ease-in-out 3s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(35, 100, 170, 0.2);
        }

        .cta-button {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .cta-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .stats-number {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(135deg, var(--mikado-yellow), var(--pumpkin));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @media (max-width: 768px) {
            .stats-number {
                font-size: 2rem;
            }
        }

        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--picton-blue), transparent);
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.5;
            animation: blob 7s infinite;
        }

        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="fas fa-qrcode text-3xl mr-3 gradient-text"></i>
                    <span class="text-2xl font-bold gradient-text">DevMenthors</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 transition">Recursos</a>
                    <a href="#how-it-works" class="text-gray-700 hover:text-blue-600 transition">Como Funciona</a>
                    <a href="#pricing" class="text-gray-700 hover:text-blue-600 transition">Preços</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 transition">Contato</a>
                </div>
                <div class="flex space-x-4">
                    <a href="index.php" class="px-4 py-2 text-gray-700 hover:text-blue-600 transition">Login</a>
                    <a href="#get-started" class="px-6 py-2 gradient-bg text-white rounded-lg hover:shadow-lg transition">Começar Grátis</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative hero-gradient pt-32 pb-20 overflow-hidden">
        <!-- Blobs animados -->
        <div class="blob w-96 h-96 bg-blue-400 top-20 left-10 animate-float"></div>
        <div class="blob w-80 h-80 bg-purple-400 bottom-20 right-10 animate-float-delay"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        Crie seu<br/>
                        <span class="text-yellow-300">Microsite</span> e<br/>
                        <span class="text-yellow-300">QR Codes</span><br/>
                        em minutos
                    </h1>
                    <p class="text-xl mb-8 text-blue-100">
                        Plataforma completa para criar microsites personalizados e gerar QR codes profissionais. Tudo em um só lugar!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="create-devmenthors.php" class="cta-button px-8 py-4 bg-yellow-400 text-gray-900 rounded-lg font-bold text-lg hover:bg-yellow-300 transition text-center">
                            <i class="fas fa-rocket mr-2"></i>
                            Criar Microsite
                        </a>
                        <a href="index.php" class="cta-button px-8 py-4 bg-white text-gray-900 rounded-lg font-bold text-lg hover:bg-gray-100 transition text-center">
                            <i class="fas fa-qrcode mr-2"></i>
                            Gerar QR Code
                        </a>
                    </div>
                    <div class="mt-8 flex items-center space-x-6 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-yellow-300 mr-2"></i>
                            <span>Grátis para começar</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-yellow-300 mr-2"></i>
                            <span>Sem cartão de crédito</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="relative z-10">
                        <img src="https://via.placeholder.com/600x400/3da5d9/ffffff?text=Preview+Microsite" alt="Preview" class="rounded-2xl shadow-2xl">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="stats-number">500+</div>
                    <p class="text-gray-600 mt-2">Microsites Criados</p>
                </div>
                <div>
                    <div class="stats-number">2K+</div>
                    <p class="text-gray-600 mt-2">QR Codes Gerados</p>
                </div>
                <div>
                    <div class="stats-number">98%</div>
                    <p class="text-gray-600 mt-2">Satisfação</p>
                </div>
                <div>
                    <div class="stats-number">24/7</div>
                    <p class="text-gray-600 mt-2">Disponibilidade</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold gradient-text mb-4">Recursos Poderosos</h2>
                <p class="text-xl text-gray-600">Tudo que você precisa em uma única plataforma</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card gradient-card rounded-2xl p-8">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-globe text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Microsites Personalizados</h3>
                    <p class="text-gray-600 mb-4">
                        Crie páginas personalizadas com widgets de links, PIX, galeria, música, vídeo, textos e localização.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>URL personalizada</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Preview em tempo real</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Tema personalizável</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>7 tipos de widgets</li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card gradient-card rounded-2xl p-8">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-qrcode text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">QR Codes Profissionais</h3>
                    <p class="text-gray-600 mb-4">
                        Gere QR codes para diversos tipos de conteúdo com personalização completa de cores e tamanhos.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>7 tipos de QR Code</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Cores personalizadas</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Múltiplos formatos</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Download em alta qualidade</li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card gradient-card rounded-2xl p-8">
                    <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">100% Responsivo</h3>
                    <p class="text-gray-600 mb-4">
                        Seus microsites ficam perfeitos em qualquer dispositivo: celular, tablet ou desktop.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Design mobile-first</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Preview duplo</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Carregamento rápido</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Interface intuitiva</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold gradient-text mb-4">Como Funciona</h2>
                <p class="text-xl text-gray-600">Simples, rápido e eficiente</p>
            </div>

            <div class="grid md:grid-cols-2 gap-16">
                <!-- Microsite Process -->
                <div>
                    <h3 class="text-2xl font-bold mb-8 flex items-center">
                        <i class="fas fa-globe text-blue-500 mr-3"></i>
                        Criar Microsite
                    </h3>
                    <div class="space-y-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white font-bold">1</div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold mb-2">Personalize seu perfil</h4>
                                <p class="text-gray-600">Adicione nome, descrição, avatar e escolha as cores do tema.</p>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white font-bold">2</div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold mb-2">Adicione widgets</h4>
                                <p class="text-gray-600">Escolha entre links, PIX, galeria, música, vídeo, textos e localização.</p>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white font-bold">3</div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold mb-2">Publique e compartilhe</h4>
                                <p class="text-gray-600">Gere QR code do seu microsite e compartilhe em suas redes sociais.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code Process -->
                <div>
                    <h3 class="text-2xl font-bold mb-8 flex items-center">
                        <i class="fas fa-qrcode text-blue-500 mr-3"></i>
                        Gerar QR Code
                    </h3>
                    <div class="space-y-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white font-bold">1</div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold mb-2">Escolha o tipo</h4>
                                <p class="text-gray-600">Selecione entre Texto, URL, E-mail, Telefone, SMS, WiFi ou vCard.</p>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white font-bold">2</div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold mb-2">Personalize</h4>
                                <p class="text-gray-600">Ajuste cores, tamanho, margem e formato de exportação.</p>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white font-bold">3</div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold mb-2">Baixe e use</h4>
                                <p class="text-gray-600">Faça download em PNG, SVG ou EPS e use onde quiser.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold gradient-text mb-4">Casos de Uso</h2>
                <p class="text-xl text-gray-600">Perfeito para diversos profissionais</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="text-5xl mb-4">👨‍💼</div>
                    <h3 class="text-xl font-bold mb-3">Profissionais</h3>
                    <p class="text-gray-600">Crie seu cartão de visitas digital com todos seus contatos e portfólio.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="text-5xl mb-4">🎨</div>
                    <h3 class="text-xl font-bold mb-3">Criativos</h3>
                    <p class="text-gray-600">Mostre seu trabalho com galerias, vídeos e links para suas redes sociais.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="text-5xl mb-4">🏪</div>
                    <h3 class="text-xl font-bold mb-3">Empreendedores</h3>
                    <p class="text-gray-600">Divulgue produtos, receba pagamentos PIX e compartilhe localização.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="text-5xl mb-4">🎵</div>
                    <h3 class="text-xl font-bold mb-3">Artistas</h3>
                    <p class="text-gray-600">Compartilhe suas músicas, shows e links de streaming em um só lugar.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="text-5xl mb-4">📱</div>
                    <h3 class="text-xl font-bold mb-3">Influencers</h3>
                    <p class="text-gray-600">Centralize todos seus links de redes sociais e parcerias.</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                    <div class="text-5xl mb-4">🍕</div>
                    <h3 class="text-xl font-bold mb-3">Restaurantes</h3>
                    <p class="text-gray-600">Menu digital, localização, pedidos e pagamentos em QR Code.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold gradient-text mb-4">Preços Transparentes</h2>
                <p class="text-xl text-gray-600">Escolha o plano ideal para você</p>
            </div>

            <div class="grid md:grid-cols-4 gap-6">
                <!-- Basic Plan -->
                <div class="bg-gray-50 rounded-2xl p-8 border-2 border-gray-200">
                    <h3 class="text-2xl font-bold mb-2">Básico</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-bold">R$ 10</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>1 Microsite</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>QR Codes ilimitados</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Todos os widgets</li>
                        <li class="flex items-center"><i class="fas fa-info-circle text-orange-500 mr-2"></i>Com marca d'água</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Suporte por email</li>
                    </ul>
                    <a href="#get-started" class="block text-center px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold hover:border-blue-500 transition">
                        Começar Agora
                    </a>
                </div>

                <!-- Starter Plan -->
                <div class="bg-gray-50 rounded-2xl p-8 border-2 border-blue-400">
                    <h3 class="text-2xl font-bold mb-2">Starter</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-bold">R$ 20</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>1 Microsite</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>QR Codes ilimitados</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Todos os widgets</li>
                        <li class="flex items-center"><i class="fas fa-star text-yellow-500 mr-2"></i>Sem marca d'água</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Suporte prioritário</li>
                    </ul>
                    <a href="#get-started" class="block text-center px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition">
                        Assinar Starter
                    </a>
                </div>

                <!-- Pro Plan -->
                <div class="gradient-bg rounded-2xl p-8 text-white transform scale-105 shadow-2xl">
                    <div class="bg-yellow-400 text-gray-900 text-xs font-bold px-3 py-1 rounded-full inline-block mb-4">POPULAR</div>
                    <h3 class="text-2xl font-bold mb-2">Pro</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-bold">R$ 70</span>
                        <span class="text-blue-100">/mês</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><i class="fas fa-check mr-2"></i>10 Microsites</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2"></i>QR Codes ilimitados</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2"></i>Widgets ilimitados</li>
                        <li class="flex items-center"><i class="fas fa-star text-yellow-300 mr-2"></i>Sem marca d'água</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2"></i>Domínio personalizado</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2"></i>Suporte prioritário</li>
                    </ul>
                    <a href="#get-started" class="block text-center px-6 py-3 bg-yellow-400 text-gray-900 rounded-lg font-bold hover:bg-yellow-300 transition">
                        Assinar Pro
                    </a>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-gray-50 rounded-2xl p-8 border-2 border-gray-200">
                    <h3 class="text-2xl font-bold mb-2">Enterprise</h3>
                    <div class="mb-6">
                        <span class="text-3xl font-bold">Personalizado</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Microsites ilimitados</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Sem marca d'água</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>API de integração</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>White label</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>SLA garantido</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Gerente dedicado</li>
                    </ul>
                    <a href="#contact" class="block text-center px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold hover:border-blue-500 transition">
                        Entre em Contato
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="get-started" class="py-20 hero-gradient relative overflow-hidden">
        <div class="blob w-96 h-96 bg-purple-400 top-10 right-10 animate-float"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Pronto para começar?</h2>
            <p class="text-xl text-blue-100 mb-8">
                Crie sua conta gratuitamente e comece a criar microsites e QR codes agora mesmo!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="create-devmenthors.php" class="cta-button px-10 py-4 bg-yellow-400 text-gray-900 rounded-lg font-bold text-lg hover:bg-yellow-300 transition inline-block">
                    <i class="fas fa-rocket mr-2"></i>
                    Criar Meu Microsite
                </a>
                <a href="index.php" class="cta-button px-10 py-4 bg-white text-gray-900 rounded-lg font-bold text-lg hover:bg-gray-100 transition inline-block">
                    <i class="fas fa-qrcode mr-2"></i>
                    Gerar QR Code Grátis
                </a>
            </div>
            <p class="mt-6 text-blue-100 text-sm">
                ✓ Sem cartão de crédito • ✓ Grátis para sempre • ✓ Cancele quando quiser
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-qrcode text-3xl mr-3 text-blue-400"></i>
                        <span class="text-2xl font-bold text-white">DevMenthors</span>
                    </div>
                    <p class="text-sm">
                        Crie microsites personalizados e QR codes profissionais em minutos.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Produto</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-white transition">Recursos</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Preços</a></li>
                        <li><a href="create-devmenthors.php" class="hover:text-white transition">Criar Microsite</a></li>
                        <li><a href="index.php" class="hover:text-white transition">Gerar QR Code</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Empresa</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Sobre</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Carreiras</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Contato</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Privacidade</a></li>
                        <li><a href="#" class="hover:text-white transition">Termos de Uso</a></li>
                        <li><a href="LICENSE" class="hover:text-white transition">Licença</a></li>
                    </ul>
                </div>
            </div>
            <div class="section-divider my-8"></div>
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm">&copy; 2025 DevMenthors. Todos os direitos reservados.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://github.com/hidalgojunior/qrcode" class="hover:text-white transition"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to top button -->
    <button id="scrollTop" class="fixed bottom-8 right-8 w-12 h-12 gradient-bg text-white rounded-full shadow-lg opacity-0 transition-opacity duration-300 hover:shadow-xl z-40">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.style.opacity = '1';
            } else {
                scrollTopBtn.style.opacity = '0';
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
