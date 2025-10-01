<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Inbox - DevMenthors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white p-6">
                <h1 class="text-3xl font-bold"><i class="fas fa-envelope-open-text mr-3"></i>Teste Sistema de Inbox</h1>
                <p class="mt-2">contato@devmenthors.shop (Hostinger)</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Teste de Conexão -->
                <div class="border rounded-lg p-4">
                    <h2 class="text-xl font-bold mb-4"><i class="fas fa-plug mr-2 text-purple-600"></i>1. Testar Conexão IMAP</h2>
                    <button onclick="testConnection()" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 font-bold">
                        <i class="fas fa-satellite-dish mr-2"></i>Testar Conexão
                    </button>
                    <div id="testResult" class="mt-4"></div>
                </div>

                <!-- Buscar Emails -->
                <div class="border rounded-lg p-4">
                    <h2 class="text-xl font-bold mb-4"><i class="fas fa-download mr-2 text-purple-600"></i>2. Buscar Novos Emails</h2>
                    <button onclick="fetchEmails()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-bold">
                        <i class="fas fa-sync-alt mr-2"></i>Buscar Emails
                    </button>
                    <div id="fetchResult" class="mt-4"></div>
                </div>

                <!-- Estatísticas -->
                <div class="border rounded-lg p-4">
                    <h2 class="text-xl font-bold mb-4"><i class="fas fa-chart-bar mr-2 text-purple-600"></i>3. Ver Estatísticas</h2>
                    <button onclick="getStats()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-bold">
                        <i class="fas fa-chart-pie mr-2"></i>Ver Stats
                    </button>
                    <div id="statsResult" class="mt-4"></div>
                </div>

                <!-- Acessar Interface -->
                <div class="border rounded-lg p-4 bg-gradient-to-r from-purple-50 to-blue-50">
                    <h2 class="text-xl font-bold mb-4"><i class="fas fa-inbox mr-2 text-purple-600"></i>4. Interface Completa</h2>
                    <a href="inbox.php" class="inline-block bg-gradient-to-r from-purple-600 to-purple-800 text-white px-8 py-4 rounded-lg hover:from-purple-700 hover:to-purple-900 font-bold text-lg">
                        <i class="fas fa-arrow-right mr-2"></i>Abrir Caixa de Entrada
                    </a>
                </div>

                <!-- Configurações -->
                <div class="border rounded-lg p-4 bg-gray-50">
                    <h2 class="text-xl font-bold mb-4"><i class="fas fa-cog mr-2 text-gray-600"></i>Configurações</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>IMAP Host:</strong> imap.hostinger.com
                        </div>
                        <div>
                            <strong>IMAP Port:</strong> 993
                        </div>
                        <div>
                            <strong>SMTP Host:</strong> smtp.hostinger.com
                        </div>
                        <div>
                            <strong>SMTP Port:</strong> 465
                        </div>
                        <div>
                            <strong>Email:</strong> contato@devmenthors.shop
                        </div>
                        <div>
                            <strong>Encryption:</strong> SSL
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function testConnection() {
            const btn = event.target;
            const resultDiv = document.getElementById('testResult');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testando...';
            resultDiv.innerHTML = '<div class="text-blue-600"><i class="fas fa-spinner fa-spin mr-2"></i>Conectando ao servidor IMAP...</div>';
            
            try {
                const response = await fetch('api/inbox.php?action=test');
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="text-green-800 font-bold text-lg mb-2">
                                <i class="fas fa-check-circle mr-2"></i>Conexão Estabelecida!
                            </h3>
                            <div class="text-green-700 space-y-1">
                                <p><strong>Mailbox:</strong> ${data.info.mailbox}</p>
                                <p><strong>Total de mensagens:</strong> ${data.info.messages}</p>
                                <p><strong>Mensagens recentes:</strong> ${data.info.recent}</p>
                                <p><strong>Não lidas:</strong> ${data.info.unread}</p>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h3 class="text-red-800 font-bold text-lg mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Erro na Conexão
                            </h3>
                            <p class="text-red-700">${data.error}</p>
                            <details class="mt-2">
                                <summary class="cursor-pointer text-red-600">Ver detalhes</summary>
                                <pre class="mt-2 text-xs bg-red-100 p-2 rounded overflow-auto">${JSON.stringify(data, null, 2)}</pre>
                            </details>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-red-800 font-bold text-lg mb-2">
                            <i class="fas fa-times-circle mr-2"></i>Erro Fatal
                        </h3>
                        <p class="text-red-700">${error.message}</p>
                    </div>
                `;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-satellite-dish mr-2"></i>Testar Conexão';
            }
        }

        async function fetchEmails() {
            const btn = event.target;
            const resultDiv = document.getElementById('fetchResult');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Buscando...';
            resultDiv.innerHTML = '<div class="text-blue-600"><i class="fas fa-spinner fa-spin mr-2"></i>Buscando emails do servidor...</div>';
            
            try {
                const response = await fetch('api/inbox.php?action=fetch');
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-blue-800 font-bold text-lg mb-2">
                                <i class="fas fa-check-circle mr-2"></i>Emails Processados!
                            </h3>
                            <div class="text-blue-700 space-y-2">
                                <p><strong>Processados:</strong> ${data.result.processed} de ${data.result.total}</p>
                                ${data.result.errors && data.result.errors.length > 0 ? `
                                    <details>
                                        <summary class="cursor-pointer text-red-600">Erros (${data.result.errors.length})</summary>
                                        <ul class="mt-2 text-xs bg-red-50 p-2 rounded">
                                            ${data.result.errors.map(e => `<li>• ${e}</li>`).join('')}
                                        </ul>
                                    </details>
                                ` : ''}
                                <div class="mt-4 pt-4 border-t">
                                    <strong>Estatísticas atuais:</strong>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div>Total: ${data.stats.total}</div>
                                        <div>Não lidos: ${data.stats.unread}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h3 class="text-red-800 font-bold text-lg mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Erro
                            </h3>
                            <p class="text-red-700">${data.error}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-red-800 font-bold text-lg mb-2">
                            <i class="fas fa-times-circle mr-2"></i>Erro Fatal
                        </h3>
                        <p class="text-red-700">${error.message}</p>
                    </div>
                `;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Buscar Emails';
            }
        }

        async function getStats() {
            const btn = event.target;
            const resultDiv = document.getElementById('statsResult');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Carregando...';
            resultDiv.innerHTML = '<div class="text-green-600"><i class="fas fa-spinner fa-spin mr-2"></i>Carregando estatísticas...</div>';
            
            try {
                const response = await fetch('api/inbox.php?action=stats');
                const data = await response.json();
                
                if (data.success) {
                    const stats = data.stats;
                    resultDiv.innerHTML = `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="text-green-800 font-bold text-lg mb-4">
                                <i class="fas fa-chart-line mr-2"></i>Estatísticas da Caixa de Entrada
                            </h3>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div class="bg-white p-3 rounded text-center">
                                    <div class="text-2xl font-bold text-purple-600">${stats.total || 0}</div>
                                    <div class="text-sm text-gray-600">Total</div>
                                </div>
                                <div class="bg-white p-3 rounded text-center">
                                    <div class="text-2xl font-bold text-yellow-600">${stats.unread || 0}</div>
                                    <div class="text-sm text-gray-600">Não Lidos</div>
                                </div>
                                <div class="bg-white p-3 rounded text-center">
                                    <div class="text-2xl font-bold text-green-600">${stats.by_category?.cliente || 0}</div>
                                    <div class="text-sm text-gray-600">Clientes</div>
                                </div>
                                <div class="bg-white p-3 rounded text-center">
                                    <div class="text-2xl font-bold text-blue-600">${stats.by_category?.suporte || 0}</div>
                                    <div class="text-sm text-gray-600">Suporte</div>
                                </div>
                            </div>
                            
                            <div class="bg-white p-3 rounded">
                                <strong>Por Status:</strong>
                                <div class="mt-2 space-y-1 text-sm">
                                    ${Object.entries(stats.by_status || {}).map(([status, count]) => `
                                        <div class="flex justify-between">
                                            <span>${status}:</span>
                                            <span class="font-bold">${count}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h3 class="text-red-800 font-bold text-lg mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Erro
                            </h3>
                            <p class="text-red-700">${data.error}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-red-800 font-bold text-lg mb-2">
                            <i class="fas fa-times-circle mr-2"></i>Erro Fatal
                        </h3>
                        <p class="text-red-700">${error.message}</p>
                    </div>
                `;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-chart-pie mr-2"></i>Ver Stats';
            }
        }
    </script>
</body>
</html>
