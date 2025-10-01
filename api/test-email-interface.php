<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Sistema de Email - DevMenthors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-2">
                📧 Teste de Sistema de Email
            </h1>
            <p class="text-gray-600">Teste o envio de emails e processamento de fila</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Formulário de Teste -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-paper-plane text-blue-500"></i> Enviar Email de Teste
                </h2>

                <form id="emailForm" class="space-y-6">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Destinatário</label>
                        <input type="email" id="to_email" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Nome -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                        <input type="text" id="to_name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Template -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                        <select id="template" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="welcome">✅ Boas-vindas</option>
                            <option value="verify-email">🔐 Verificação de Email</option>
                            <option value="reset-password">🔑 Recuperação de Senha</option>
                            <option value="payment-confirmed">💰 Pagamento Confirmado</option>
                            <option value="subscription-expiring">⏰ Assinatura Expirando</option>
                        </select>
                    </div>

                    <!-- Usar Fila -->
                    <div class="flex items-center">
                        <input type="checkbox" id="use_queue" checked
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="use_queue" class="ml-2 text-sm text-gray-700">Usar fila de emails</label>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-3">
                        <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 rounded-lg font-semibold hover:shadow-lg transition-all">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Email
                        </button>
                    </div>
                </form>

                <!-- Teste de Conexão -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <button onclick="testConnection()" 
                        class="w-full py-3 bg-blue-100 text-blue-700 rounded-lg font-medium hover:bg-blue-200 transition-all">
                        <i class="fas fa-check-circle mr-2"></i>
                        Testar Conexão SMTP
                    </button>
                </div>
            </div>

            <!-- Fila e Status -->
            <div class="space-y-6">
                <!-- Status da Fila -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-list text-purple-500"></i> Fila de Emails
                    </h3>
                    <button onclick="loadQueue()" 
                        class="w-full py-3 bg-purple-100 text-purple-700 rounded-lg font-medium hover:bg-purple-200 transition-all mb-4">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Carregar Fila
                    </button>
                    <div id="queueList" class="space-y-2">
                        <p class="text-gray-400 text-center py-4">Clique em "Carregar Fila"</p>
                    </div>
                </div>

                <!-- Processar Fila -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-cogs text-green-500"></i> Processar Fila
                    </h3>
                    <button onclick="processQueue()" 
                        class="w-full py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-all">
                        <i class="fas fa-play mr-2"></i>
                        Processar Agora
                    </button>
                    <p class="text-sm text-gray-500 mt-3">Processa até 10 emails da fila</p>
                </div>

                <!-- Resultado -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Resultado</h3>
                    <div id="result" class="hidden">
                        <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-x-auto"></pre>
                    </div>
                    <div id="noResult" class="text-center py-8 text-gray-400">
                        <i class="fas fa-inbox text-5xl mb-3"></i>
                        <p>Nenhum resultado ainda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enviar email
        document.getElementById('emailForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const data = {
                to_email: document.getElementById('to_email').value,
                to_name: document.getElementById('to_name').value,
                template: document.getElementById('template').value,
                use_queue: document.getElementById('use_queue').checked
            };

            showResult('⏳ Enviando email...', 'loading');

            try {
                const response = await fetch('/QrCode/api/test-email.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                showResult(JSON.stringify(result, null, 2), response.ok ? 'success' : 'error');

                if (response.ok && data.use_queue) {
                    setTimeout(loadQueue, 500);
                }
            } catch (error) {
                showResult(`Erro: ${error.message}`, 'error');
            }
        });

        // Testar conexão
        async function testConnection() {
            showResult('⏳ Testando conexão SMTP...', 'loading');

            try {
                const response = await fetch('/QrCode/api/test-email.php?action=test-connection');
                const result = await response.json();
                showResult(JSON.stringify(result, null, 2), response.ok ? 'success' : 'error');
            } catch (error) {
                showResult(`Erro: ${error.message}`, 'error');
            }
        }

        // Carregar fila
        async function loadQueue() {
            const listDiv = document.getElementById('queueList');
            listDiv.innerHTML = '<p class="text-gray-400 text-center py-4">Carregando...</p>';

            try {
                const response = await fetch('/QrCode/api/test-email.php?action=queue-list');
                const data = await response.json();

                if (data.success && data.queue.length > 0) {
                    listDiv.innerHTML = data.queue.map(e => `
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-gray-800">#${e.id}</span>
                                    <span class="text-sm text-gray-500 ml-2">${e.to_email}</span>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full ${getStatusClass(e.status)}">
                                    ${e.status}
                                </span>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">${e.subject}</div>
                        </div>
                    `).join('');
                } else {
                    listDiv.innerHTML = '<p class="text-gray-400 text-center py-4">Fila vazia</p>';
                }
            } catch (error) {
                listDiv.innerHTML = `<p class="text-red-500 text-center py-4">Erro: ${error.message}</p>`;
            }
        }

        // Processar fila
        async function processQueue() {
            showResult('⏳ Processando fila...', 'loading');

            try {
                const response = await fetch('/QrCode/api/test-email.php?action=process-queue');
                const result = await response.json();
                showResult(JSON.stringify(result, null, 2), response.ok ? 'success' : 'error');
                setTimeout(loadQueue, 500);
            } catch (error) {
                showResult(`Erro: ${error.message}`, 'error');
            }
        }

        // Mostrar resultado
        function showResult(content, type) {
            const resultDiv = document.getElementById('result');
            const noResultDiv = document.getElementById('noResult');
            const pre = resultDiv.querySelector('pre');

            resultDiv.classList.remove('hidden');
            noResultDiv.classList.add('hidden');
            pre.textContent = content;

            if (type === 'success') {
                pre.className = 'bg-green-50 border-2 border-green-200 rounded-lg p-4 text-sm overflow-x-auto';
            } else if (type === 'error') {
                pre.className = 'bg-red-50 border-2 border-red-200 rounded-lg p-4 text-sm overflow-x-auto';
            } else {
                pre.className = 'bg-blue-50 border-2 border-blue-200 rounded-lg p-4 text-sm overflow-x-auto';
            }
        }

        // Classe de status
        function getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-700',
                'processing': 'bg-blue-100 text-blue-700',
                'sent': 'bg-green-100 text-green-700',
                'failed': 'bg-red-100 text-red-700'
            };
            return classes[status] || 'bg-gray-100 text-gray-700';
        }
    </script>
</body>
</html>
