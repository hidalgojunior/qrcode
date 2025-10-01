<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos QR Code - DevMenthors</title>
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
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue));
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--green-blue), var(--picton-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .plan-card {
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <a href="home.php" class="inline-flex items-center mb-6">
                <i class="fas fa-qrcode text-4xl gradient-text mr-3"></i>
                <span class="text-3xl font-bold gradient-text">DevMenthors</span>
            </a>
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Planos QR Code</h1>
            <p class="text-xl text-gray-600">Escolha o plano perfeito para suas necessidades</p>
        </div>

        <!-- Plans -->
        <div class="grid md:grid-cols-4 gap-6 max-w-6xl mx-auto mb-12">
            <!-- Free Plan -->
            <div class="plan-card bg-white rounded-2xl shadow-xl p-6 border-2 border-gray-200">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-qrcode text-3xl text-gray-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Grátis</h3>
                    <div class="text-4xl font-bold text-gray-800 mb-2">R$ 0</div>
                    <p class="text-gray-600 text-sm">/mês</p>
                </div>

                <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>QR Codes ilimitados</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>7 tipos de QR Code</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                        <span><strong>Com marca d'água</strong></span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-times text-red-500 mr-2"></i>
                        <span>Sem personalização</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-download text-blue-500 mr-2"></i>
                        <span>Download PNG básico</span>
                    </li>
                </ul>

                <a href="index.php" 
                   class="block text-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-blue-500 transition font-semibold">
                    Começar Grátis
                </a>
            </div>

            <!-- Starter Plan -->
            <div class="plan-card bg-white rounded-2xl shadow-xl p-6 border-2 border-blue-400">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto gradient-bg rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-qrcode text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Starter</h3>
                    <div class="text-4xl font-bold gradient-text mb-2">R$ 20</div>
                    <p class="text-gray-600 text-sm">/mês</p>
                </div>

                <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span><strong>Até 10 QR Codes/mês</strong></span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        <span><strong>Sem marca d'água</strong></span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-palette text-purple-500 mr-2"></i>
                        <span>Personalização de cores</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-expand text-blue-500 mr-2"></i>
                        <span>Tamanhos customizados</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-download text-blue-500 mr-2"></i>
                        <span>PNG, SVG, EPS</span>
                    </li>
                </ul>

                <button onclick="selectPlan('starter', 20)" 
                        class="block w-full text-center px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-bold">
                    Assinar Starter
                </button>
            </div>

            <!-- Pro Plan -->
            <div class="plan-card bg-white rounded-2xl shadow-xl p-6 border-2 border-blue-600 relative transform scale-105">
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold px-4 py-1 rounded-full shadow-lg">
                        MAIS POPULAR
                    </span>
                </div>

                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto gradient-bg rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-crown text-3xl text-yellow-300"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Pro</h3>
                    <div class="text-4xl font-bold gradient-text mb-2">R$ 30</div>
                    <p class="text-gray-600 text-sm">/mês</p>
                </div>

                <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-sm">
                        <i class="fas fa-infinity text-blue-500 mr-2"></i>
                        <span><strong>Até 50 QR Codes/mês</strong></span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        <span><strong>Sem marca d'água</strong></span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-palette text-purple-500 mr-2"></i>
                        <span>Personalização completa</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-chart-line text-green-500 mr-2"></i>
                        <span>Analytics avançado</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-download text-blue-500 mr-2"></i>
                        <span>Todos os formatos</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-headset text-blue-500 mr-2"></i>
                        <span>Suporte prioritário</span>
                    </li>
                </ul>

                <button onclick="selectPlan('pro', 30)" 
                        class="block w-full text-center px-6 py-3 gradient-bg text-white rounded-lg hover:shadow-xl transition font-bold">
                    Assinar Pro
                </button>
            </div>

            <!-- Enterprise Plan -->
            <div class="plan-card bg-white rounded-2xl shadow-xl p-6 border-2 border-gray-200">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-building text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Enterprise</h3>
                    <div class="text-3xl font-bold text-gray-800 mb-2">Personalizado</div>
                    <p class="text-gray-600 text-sm">Contato</p>
                </div>

                <ul class="space-y-3 mb-8">
                    <li class="flex items-center text-sm">
                        <i class="fas fa-infinity text-blue-500 mr-2"></i>
                        <span><strong>QR Codes ilimitados</strong></span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        <span>Sem marca d'água</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-code text-purple-500 mr-2"></i>
                        <span>API de integração</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                        <span>SLA garantido</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-user-tie text-blue-500 mr-2"></i>
                        <span>Gerente dedicado</span>
                    </li>
                    <li class="flex items-center text-sm">
                        <i class="fas fa-cog text-gray-500 mr-2"></i>
                        <span>Customizações</span>
                    </li>
                </ul>

                <a href="mailto:contato@devmenthors.com" 
                   class="block text-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-blue-500 transition font-semibold">
                    Entre em Contato
                </a>
            </div>
        </div>

        <!-- Comparison Table -->
        <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-8 mb-12">
            <h2 class="text-3xl font-bold text-center gradient-text mb-8">Compare os Planos</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-4 px-4">Recursos</th>
                            <th class="text-center py-4 px-4">Grátis</th>
                            <th class="text-center py-4 px-4">Starter</th>
                            <th class="text-center py-4 px-4 bg-blue-50">Pro</th>
                            <th class="text-center py-4 px-4">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4 font-semibold">QR Codes por mês</td>
                            <td class="text-center py-4 px-4">Ilimitado*</td>
                            <td class="text-center py-4 px-4">10</td>
                            <td class="text-center py-4 px-4 bg-blue-50">50</td>
                            <td class="text-center py-4 px-4">Ilimitado</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4 font-semibold">Marca d'água</td>
                            <td class="text-center py-4 px-4"><i class="fas fa-check text-orange-500"></i></td>
                            <td class="text-center py-4 px-4"><i class="fas fa-times text-red-500"></i></td>
                            <td class="text-center py-4 px-4 bg-blue-50"><i class="fas fa-times text-red-500"></i></td>
                            <td class="text-center py-4 px-4"><i class="fas fa-times text-red-500"></i></td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4 font-semibold">Personalização de cores</td>
                            <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-4 bg-blue-50"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4 font-semibold">Tamanhos customizados</td>
                            <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-4 bg-blue-50"><i class="fas fa-check text-green-500"></i></td>
                            <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4 font-semibold">Formatos de download</td>
                            <td class="text-center py-4 px-4 text-sm">PNG</td>
                            <td class="text-center py-4 px-4 text-sm">PNG, SVG, EPS</td>
                            <td class="text-center py-4 px-4 bg-blue-50 text-sm">Todos</td>
                            <td class="text-center py-4 px-4 text-sm">Todos</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4 font-semibold">Analytics</td>
                            <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="text-center py-4 px-4">Básico</td>
                            <td class="text-center py-4 px-4 bg-blue-50">Avançado</td>
                            <td class="text-center py-4 px-4">Completo</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-4 px-4 font-semibold">Suporte</td>
                            <td class="text-center py-4 px-4 text-sm">Email</td>
                            <td class="text-center py-4 px-4 text-sm">Email</td>
                            <td class="text-center py-4 px-4 bg-blue-50 text-sm">Prioritário</td>
                            <td class="text-center py-4 px-4 text-sm">Dedicado</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 font-semibold">API de integração</td>
                            <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="text-center py-4 px-4"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="text-center py-4 px-4 bg-blue-50"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="text-center py-4 px-4"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <p class="text-xs text-gray-500 mt-4">* Plano grátis: todos os QR Codes incluem marca d'água</p>
        </div>

        <!-- FAQ -->
        <div class="max-w-3xl mx-auto mb-12">
            <h2 class="text-3xl font-bold text-center gradient-text mb-8">Perguntas Frequentes</h2>
            
            <div class="space-y-4">
                <details class="bg-white rounded-lg shadow p-6">
                    <summary class="font-bold text-gray-800 cursor-pointer">O que é a marca d'água no plano grátis?</summary>
                    <p class="text-gray-600 mt-3 text-sm">
                        No plano grátis, os QR Codes gerados incluem um pequeno texto "DevMenthors" na parte inferior. 
                        Nos planos pagos, seus QR Codes são totalmente limpos e profissionais.
                    </p>
                </details>
                
                <details class="bg-white rounded-lg shadow p-6">
                    <summary class="font-bold text-gray-800 cursor-pointer">Posso fazer upgrade depois?</summary>
                    <p class="text-gray-600 mt-3 text-sm">
                        Sim! Você pode fazer upgrade do seu plano a qualquer momento. O valor será ajustado proporcionalmente.
                    </p>
                </details>
                
                <details class="bg-white rounded-lg shadow p-6">
                    <summary class="font-bold text-gray-800 cursor-pointer">Os QR Codes expiram?</summary>
                    <p class="text-gray-600 mt-3 text-sm">
                        Não! Uma vez gerado, seu QR Code funciona para sempre, mesmo que você cancele sua assinatura.
                    </p>
                </details>
            </div>
        </div>

        <!-- CTA -->
        <div class="text-center">
            <a href="register.php" 
               class="inline-block px-8 py-4 gradient-bg text-white rounded-lg font-bold text-lg hover:shadow-xl transition">
                <i class="fas fa-rocket mr-2"></i>Começar Agora Grátis
            </a>
        </div>
    </div>

    <script>
        function selectPlan(plan, price) {
            // Redirecionar para página de cadastro/login com plano selecionado
            window.location.href = `register.php?plan=${plan}&price=${price}`;
        }
    </script>
</body>
</html>
