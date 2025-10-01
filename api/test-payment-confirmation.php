<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Webhook - DevMenthors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-2">
                🧪 Teste de Webhook PIX
            </h1>
            <p class="text-gray-600">Simule confirmações de pagamento para testes</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Formulário de Teste -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-rocket text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Simular Pagamento</h2>
                        <p class="text-sm text-gray-500">Envie um webhook de teste</p>
                    </div>
                </div>

                <form id="webhookForm" class="space-y-6">
                    <!-- Payment ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hashtag text-blue-500"></i> Payment ID
                        </label>
                        <input type="number" id="payment_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ex: 1">
                        <p class="text-xs text-gray-500 mt-1">ID do pagamento no banco de dados</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check-circle text-green-500"></i> Status
                        </label>
                        <select id="status" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="paid">✅ Pago (paid)</option>
                            <option value="approved">✅ Aprovado (approved)</option>
                            <option value="confirmed">✅ Confirmado (confirmed)</option>
                            <option value="expired">⏰ Expirado (expired)</option>
                            <option value="cancelled">❌ Cancelado (cancelled)</option>
                            <option value="failed">❌ Falhou (failed)</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign text-green-500"></i> Valor (R$)
                        </label>
                        <input type="number" id="amount" step="0.01" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ex: 20.00">
                    </div>

                    <!-- Transaction ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-barcode text-purple-500"></i> Transaction ID
                        </label>
                        <input type="text" id="transaction_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ex: TXN_1234567890">
                        <button type="button" onclick="generateTxnId()" 
                            class="text-sm text-blue-600 hover:text-blue-700 mt-1">
                            <i class="fas fa-sync-alt"></i> Gerar automaticamente
                        </button>
                    </div>

                    <!-- Webhook Secret (opcional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key text-red-500"></i> Webhook Secret (opcional)
                        </label>
                        <input type="text" id="webhook_secret"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="devmenthors_webhook_secret_key_2025"
                            value="devmenthors_webhook_secret_key_2025">
                        <p class="text-xs text-gray-500 mt-1">Para gerar assinatura HMAC SHA256</p>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-3">
                        <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 rounded-lg font-semibold hover:shadow-lg transition-all duration-300 hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Webhook
                        </button>
                        <button type="button" onclick="clearForm()"
                            class="px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>
                </form>

                <!-- Buscar Pagamentos -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-search text-blue-500"></i> Buscar Pagamentos
                    </h3>
                    <button onclick="loadPayments()" 
                        class="w-full py-3 bg-blue-100 text-blue-700 rounded-lg font-medium hover:bg-blue-200 transition-all">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Carregar Pagamentos Pendentes
                    </button>
                </div>
            </div>

            <!-- Resultado e Logs -->
            <div class="space-y-6">
                <!-- Resultado -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-terminal text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Resposta</h2>
                            <p class="text-sm text-gray-500">Resultado do webhook</p>
                        </div>
                    </div>

                    <div id="result" class="hidden">
                        <div id="resultContent" class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                            <pre class="text-sm text-gray-700"></pre>
                        </div>
                    </div>

                    <div id="noResult" class="text-center py-8 text-gray-400">
                        <i class="fas fa-inbox text-5xl mb-3"></i>
                        <p>Nenhum resultado ainda</p>
                        <p class="text-sm">Envie um webhook para ver a resposta</p>
                    </div>
                </div>

                <!-- Lista de Pagamentos -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-list text-purple-500"></i> Pagamentos Recentes
                    </h3>
                    <div id="paymentsList" class="space-y-2">
                        <p class="text-gray-400 text-center py-4">
                            Clique em "Carregar Pagamentos" acima
                        </p>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        <i class="fas fa-bolt text-yellow-500"></i> Ações Rápidas
                    </h3>
                    <div class="space-y-2">
                        <button onclick="testSuccessfulPayment()" 
                            class="w-full py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all text-sm">
                            ✅ Simular Pagamento Aprovado
                        </button>
                        <button onclick="testExpiredPayment()" 
                            class="w-full py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all text-sm">
                            ⏰ Simular Pagamento Expirado
                        </button>
                        <button onclick="viewLogs()" 
                            class="w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all text-sm">
                            📋 Ver Logs do Webhook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gerar Transaction ID aleatório
        function generateTxnId() {
            const txnId = 'TXN_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9).toUpperCase();
            document.getElementById('transaction_id').value = txnId;
        }

        // Limpar formulário
        function clearForm() {
            document.getElementById('webhookForm').reset();
            document.getElementById('webhook_secret').value = 'devmenthors_webhook_secret_key_2025';
        }

        // Enviar webhook
        document.getElementById('webhookForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = {
                payment_id: parseInt(document.getElementById('payment_id').value),
                status: document.getElementById('status').value,
                amount: parseFloat(document.getElementById('amount').value),
                transaction_id: document.getElementById('transaction_id').value
            };

            const webhookSecret = document.getElementById('webhook_secret').value;

            // Gerar assinatura HMAC
            let signature = null;
            if (webhookSecret) {
                const payload = JSON.stringify(formData);
                const encoder = new TextEncoder();
                const keyData = encoder.encode(webhookSecret);
                const messageData = encoder.encode(payload);
                
                const cryptoKey = await crypto.subtle.importKey(
                    'raw', keyData, { name: 'HMAC', hash: 'SHA-256' }, false, ['sign']
                );
                const signatureBuffer = await crypto.subtle.sign('HMAC', cryptoKey, messageData);
                signature = Array.from(new Uint8Array(signatureBuffer))
                    .map(b => b.toString(16).padStart(2, '0')).join('');
            }

            // Mostrar loading
            showResult('⏳ Enviando webhook...', 'loading');

            try {
                const headers = {
                    'Content-Type': 'application/json'
                };
                
                if (signature) {
                    headers['X-Webhook-Signature'] = signature;
                }

                const response = await fetch('/QrCode/api/webhook-payment.php', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                
                const fullResult = {
                    status: response.status,
                    statusText: response.statusText,
                    headers: {
                        'X-Webhook-Signature': signature || 'não enviada'
                    },
                    request: formData,
                    response: result
                };

                if (response.ok) {
                    showResult(JSON.stringify(fullResult, null, 2), 'success');
                } else {
                    showResult(JSON.stringify(fullResult, null, 2), 'error');
                }
            } catch (error) {
                showResult(`Erro: ${error.message}`, 'error');
            }
        });

        // Mostrar resultado
        function showResult(content, type) {
            const resultDiv = document.getElementById('result');
            const noResultDiv = document.getElementById('noResult');
            const pre = resultDiv.querySelector('pre');

            resultDiv.classList.remove('hidden');
            noResultDiv.classList.add('hidden');

            pre.textContent = content;

            // Cores baseadas no tipo
            const contentDiv = document.getElementById('resultContent');
            if (type === 'success') {
                contentDiv.className = 'bg-green-50 border-2 border-green-200 rounded-lg p-4 overflow-x-auto';
            } else if (type === 'error') {
                contentDiv.className = 'bg-red-50 border-2 border-red-200 rounded-lg p-4 overflow-x-auto';
            } else {
                contentDiv.className = 'bg-blue-50 border-2 border-blue-200 rounded-lg p-4 overflow-x-auto';
            }
        }

        // Carregar pagamentos
        async function loadPayments() {
            const listDiv = document.getElementById('paymentsList');
            listDiv.innerHTML = '<p class="text-gray-400 text-center py-4">Carregando...</p>';

            try {
                const response = await fetch('/QrCode/api/list-payments.php');
                const data = await response.json();

                if (data.success && data.payments.length > 0) {
                    listDiv.innerHTML = data.payments.map(p => `
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-all cursor-pointer"
                             onclick="fillPaymentForm(${p.id}, ${p.amount})">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-gray-800">#${p.id}</span>
                                    <span class="text-sm text-gray-500 ml-2">${p.plan_name}</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-green-600">R$ ${parseFloat(p.amount).toFixed(2)}</div>
                                    <span class="text-xs px-2 py-1 rounded-full ${getStatusClass(p.status)}">
                                        ${p.status}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    listDiv.innerHTML = '<p class="text-gray-400 text-center py-4">Nenhum pagamento encontrado</p>';
                }
            } catch (error) {
                listDiv.innerHTML = `<p class="text-red-500 text-center py-4">Erro: ${error.message}</p>`;
            }
        }

        // Preencher formulário
        function fillPaymentForm(id, amount) {
            document.getElementById('payment_id').value = id;
            document.getElementById('amount').value = amount;
            generateTxnId();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Classe de status
        function getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-700',
                'paid': 'bg-green-100 text-green-700',
                'expired': 'bg-red-100 text-red-700',
                'cancelled': 'bg-gray-100 text-gray-700'
            };
            return classes[status] || 'bg-gray-100 text-gray-700';
        }

        // Teste rápido - aprovado
        function testSuccessfulPayment() {
            document.getElementById('payment_id').value = '1';
            document.getElementById('status').value = 'paid';
            document.getElementById('amount').value = '20.00';
            generateTxnId();
            alert('✅ Formulário preenchido para simular pagamento aprovado!\nClique em "Enviar Webhook".');
        }

        // Teste rápido - expirado
        function testExpiredPayment() {
            document.getElementById('payment_id').value = '1';
            document.getElementById('status').value = 'expired';
            document.getElementById('amount').value = '20.00';
            generateTxnId();
            alert('⏰ Formulário preenchido para simular pagamento expirado!\nClique em "Enviar Webhook".');
        }

        // Ver logs
        function viewLogs() {
            window.open('/QrCode/logs/webhook-' + new Date().toISOString().split('T')[0] + '.log', '_blank');
        }

        // Gerar Transaction ID ao carregar
        window.addEventListener('load', generateTxnId);
    </script>
</body>
</html>
