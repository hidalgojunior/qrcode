<?php
require_once __DIR__ . '/../includes/database.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getUser();
$db = Database::getInstance();

// Buscar todos os planos
$plans = $db->fetchAll("SELECT * FROM plans WHERE status = 'active' ORDER BY price ASC");

// Buscar assinatura atual
$currentSubscription = $db->fetch(
    "SELECT s.*, p.name as plan_name 
     FROM subscriptions s 
     JOIN plans p ON s.plan_id = p.id 
     WHERE s.user_id = ? AND s.status IN ('active', 'pending') 
     ORDER BY s.created_at DESC LIMIT 1",
    [$auth->getUserId()]
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assinatura - DevMenthors</title>
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

        .plan-card.featured {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-8 ml-64 mt-16">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Planos e Assinatura</h1>
                    <p class="text-gray-600">Escolha o melhor plano para você</p>
                </div>

                <!-- Current Plan -->
                <?php if ($currentSubscription): ?>
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">
                                <i class="fas fa-crown text-yellow-500 mr-2"></i>
                                Sua Assinatura Atual
                            </h3>
                            <p class="text-gray-600">
                                Plano: <span class="font-semibold"><?php echo $currentSubscription['plan_name']; ?></span>
                            </p>
                            <p class="text-gray-600">
                                Status: 
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?php echo $currentSubscription['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                    <?php echo $currentSubscription['status'] === 'active' ? 'Ativo' : 'Pendente'; ?>
                                </span>
                            </p>
                            <?php if ($currentSubscription['status'] === 'active'): ?>
                            <p class="text-gray-600">
                                Vence em: <span class="font-semibold"><?php echo date('d/m/Y', strtotime($currentSubscription['end_date'])); ?></span>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($currentSubscription['status'] === 'active'): ?>
                        <button onclick="cancelSubscription()" 
                                class="px-6 py-2 border-2 border-red-500 text-red-600 rounded-lg hover:bg-red-50 transition">
                            Cancelar Assinatura
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Plans -->
                <div class="grid md:grid-cols-4 gap-6">
                    <?php foreach ($plans as $plan): ?>
                    <div class="plan-card <?php echo $plan['slug'] === 'pro' ? 'featured' : ''; ?> 
                                bg-white rounded-2xl shadow-lg p-6 relative
                                <?php echo $plan['slug'] === 'pro' ? 'ring-2 ring-blue-500' : ''; ?>">
                        
                        <?php if ($plan['slug'] === 'pro'): ?>
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <span class="bg-yellow-400 text-gray-900 text-xs font-bold px-4 py-1 rounded-full">
                                POPULAR
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-800 mb-2"><?php echo $plan['name']; ?></h3>
                            <div class="text-4xl font-bold gradient-text mb-2">
                                R$ <?php echo number_format($plan['price'], 0, ',', '.'); ?>
                            </div>
                            <p class="text-gray-600 text-sm">/mês</p>
                        </div>

                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>
                                    <?php echo $plan['microsites_limit'] ? $plan['microsites_limit'] . ' Microsite' . ($plan['microsites_limit'] > 1 ? 's' : '') : 'Microsites ilimitados'; ?>
                                </span>
                            </li>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>QR Codes ilimitados</span>
                            </li>
                            <li class="flex items-center text-sm">
                                <?php if ($plan['has_watermark']): ?>
                                <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                                <span>Com marca d'água</span>
                                <?php else: ?>
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Sem marca d'água</span>
                                <?php endif; ?>
                            </li>
                            <?php if ($plan['custom_domain']): ?>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Domínio personalizado</span>
                            </li>
                            <?php endif; ?>
                            <?php if ($plan['priority_support']): ?>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Suporte prioritário</span>
                            </li>
                            <?php endif; ?>
                            <?php if ($plan['api_access']): ?>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Acesso API</span>
                            </li>
                            <?php endif; ?>
                            <?php if ($plan['white_label']): ?>
                            <li class="flex items-center text-sm">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>White label</span>
                            </li>
                            <?php endif; ?>
                        </ul>

                        <?php if ($plan['slug'] === 'enterprise'): ?>
                        <button onclick="contactEnterprise()" 
                                class="w-full py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-blue-500 transition font-semibold">
                            Entre em Contato
                        </button>
                        <?php elseif ($user['plan_id'] == $plan['id']): ?>
                        <button disabled
                                class="w-full py-3 bg-gray-200 text-gray-500 rounded-lg font-semibold cursor-not-allowed">
                            Plano Atual
                        </button>
                        <?php else: ?>
                        <button onclick="selectPlan(<?php echo $plan['id']; ?>, '<?php echo $plan['name']; ?>', <?php echo $plan['price']; ?>)" 
                                class="w-full py-3 <?php echo $plan['slug'] === 'pro' ? 'gradient-bg text-white' : 'border-2 border-blue-500 text-blue-600 hover:bg-blue-50'; ?> rounded-lg transition font-semibold">
                            Assinar Agora
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- FAQ -->
                <div class="mt-12 bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Perguntas Frequentes</h2>
                    <div class="space-y-4 max-w-3xl mx-auto">
                        <details class="border-b border-gray-200 pb-4">
                            <summary class="font-semibold text-gray-800 cursor-pointer hover:text-blue-600">
                                Como funciona o pagamento?
                            </summary>
                            <p class="text-gray-600 mt-2 text-sm">
                                Aceitamos pagamentos via PIX. Após escolher seu plano, você receberá um QR Code para pagamento. 
                                Assim que confirmarmos o pagamento, sua assinatura será ativada automaticamente.
                            </p>
                        </details>
                        
                        <details class="border-b border-gray-200 pb-4">
                            <summary class="font-semibold text-gray-800 cursor-pointer hover:text-blue-600">
                                Posso cancelar a qualquer momento?
                            </summary>
                            <p class="text-gray-600 mt-2 text-sm">
                                Sim! Você pode cancelar sua assinatura a qualquer momento. 
                                Você continuará tendo acesso aos recursos até o fim do período pago.
                            </p>
                        </details>
                        
                        <details class="border-b border-gray-200 pb-4">
                            <summary class="font-semibold text-gray-800 cursor-pointer hover:text-blue-600">
                                O que acontece se eu não renovar?
                            </summary>
                            <p class="text-gray-600 mt-2 text-sm">
                                Seus microsites permanecerão ativos em modo somente leitura. 
                                Você poderá visualizá-los mas não poderá editá-los ou criar novos.
                            </p>
                        </details>
                        
                        <details class="border-b border-gray-200 pb-4">
                            <summary class="font-semibold text-gray-800 cursor-pointer hover:text-blue-600">
                                Posso mudar de plano?
                            </summary>
                            <p class="text-gray-600 mt-2 text-sm">
                                Sim! Você pode fazer upgrade ou downgrade do seu plano a qualquer momento. 
                                O valor será ajustado proporcionalmente.
                            </p>
                        </details>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal PIX Payment -->
    <div id="pixModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Pagamento via PIX</h3>
                <p class="text-gray-600 mb-4">
                    Plano: <span id="selectedPlanName" class="font-semibold"></span>
                </p>
                <div class="text-3xl font-bold gradient-text mb-6">
                    R$ <span id="selectedPlanPrice"></span>
                </div>

                <!-- QR Code -->
                <div id="pixQRCode" class="bg-gray-100 rounded-lg p-6 mb-4">
                    <div class="animate-pulse">
                        <div class="h-64 w-64 mx-auto bg-gray-300 rounded"></div>
                    </div>
                </div>

                <!-- PIX Code -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Código PIX</label>
                    <div class="flex">
                        <input type="text" id="pixCode" readonly
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg bg-gray-50 text-sm">
                        <button onclick="copyPixCode()" 
                                class="px-4 py-2 gradient-bg text-white rounded-r-lg hover:shadow-lg transition">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6 text-left">
                    <h4 class="font-semibold text-blue-800 mb-2">Como pagar:</h4>
                    <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                        <li>Abra o app do seu banco</li>
                        <li>Escolha pagar via PIX</li>
                        <li>Escaneie o QR Code ou cole o código</li>
                        <li>Confirme o pagamento</li>
                    </ol>
                </div>

                <div class="flex space-x-4">
                    <button onclick="closePixModal()" 
                            class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button onclick="confirmPayment()" 
                            class="flex-1 px-6 py-3 gradient-bg text-white rounded-lg hover:shadow-lg transition">
                            Já Paguei
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedPlanId = null;

        function selectPlan(planId, planName, planPrice) {
            selectedPlanId = planId;
            document.getElementById('selectedPlanName').textContent = planName;
            document.getElementById('selectedPlanPrice').textContent = planPrice.toFixed(2).replace('.', ',');
            
            // Gerar pagamento PIX
            generatePixPayment(planId);
            
            // Mostrar modal
            document.getElementById('pixModal').classList.remove('hidden');
            document.getElementById('pixModal').classList.add('flex');
        }

        async function generatePixPayment(planId) {
            try {
                const response = await fetch('../api/create-payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ plan_id: planId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('pixCode').value = data.pix_code;
                    document.getElementById('pixQRCode').innerHTML = `
                        <img src="${data.qrcode_url}" alt="QR Code PIX" class="w-64 h-64 mx-auto">
                    `;
                }
            } catch (error) {
                console.error('Erro ao gerar pagamento:', error);
            }
        }

        function closePixModal() {
            document.getElementById('pixModal').classList.add('hidden');
            document.getElementById('pixModal').classList.remove('flex');
        }

        function copyPixCode() {
            const pixCode = document.getElementById('pixCode');
            pixCode.select();
            document.execCommand('copy');
            
            alert('Código PIX copiado!');
        }

        function confirmPayment() {
            // TODO: Verificar status do pagamento
            alert('Estamos verificando seu pagamento. Você receberá uma notificação assim que for confirmado.');
            closePixModal();
        }

        function cancelSubscription() {
            if (confirm('Tem certeza que deseja cancelar sua assinatura?')) {
                // TODO: Implementar cancelamento
                window.location.href = '../api/cancel-subscription.php';
            }
        }

        function contactEnterprise() {
            window.location.href = 'support.php?plan=enterprise';
        }
    </script>
</body>
</html>
