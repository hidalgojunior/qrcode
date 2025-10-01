<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa de Entrada - DevMenthors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .email-body { max-height: 500px; overflow-y: auto; }
        .badge { @apply px-3 py-1 rounded-full text-xs font-bold; }
        .badge-cliente { @apply bg-green-100 text-green-800; }
        .badge-spam { @apply bg-red-100 text-red-800; }
        .badge-suporte { @apply bg-blue-100 text-blue-800; }
        .badge-geral { @apply bg-gray-100 text-gray-800; }
        .badge-unread { @apply bg-yellow-100 text-yellow-800; }
        .badge-read { @apply bg-gray-100 text-gray-800; }
        .badge-replied { @apply bg-green-100 text-green-800; }
        .email-item:hover { @apply bg-gray-50; }
        .email-item.unread { @apply bg-blue-50 font-semibold; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-purple-600 to-purple-800 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold"><i class="fas fa-inbox mr-2"></i>Caixa de Entrada</h1>
                        <p class="text-purple-200 mt-1">contato@devmenthors.shop</p>
                    </div>
                    <div class="text-right">
                        <button onclick="fetchEmails()" class="bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50 transition">
                            <i class="fas fa-sync-alt mr-2"></i>Buscar Novos
                        </button>
                        <a href="dashboard.php" class="ml-2 bg-purple-700 px-4 py-2 rounded-lg hover:bg-purple-600 transition">
                            <i class="fas fa-arrow-left mr-2"></i>Voltar
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="container mx-auto px-4 py-8">
            <!-- Estatísticas -->
            <div id="stats" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <!-- Carregando via JS -->
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" id="search" placeholder="Buscar emails..." 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500"
                               onkeyup="if(event.key==='Enter') loadEmails()">
                    </div>
                    <select id="categoryFilter" onchange="loadEmails()" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todas Categorias</option>
                        <option value="cliente">Cliente</option>
                        <option value="suporte">Suporte</option>
                        <option value="geral">Geral</option>
                        <option value="spam">Spam</option>
                    </select>
                    <select id="statusFilter" onchange="loadEmails()" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todos Status</option>
                        <option value="unread" selected>Não Lidos</option>
                        <option value="read">Lidos</option>
                        <option value="replied">Respondidos</option>
                    </select>
                    <button onclick="loadEmails()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Lista de Emails -->
                <div class="lg:col-span-1 bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-4">
                        <h2 class="text-xl font-bold"><i class="fas fa-envelope mr-2"></i>Mensagens</h2>
                    </div>
                    <div id="emailList" class="overflow-y-auto" style="max-height: 600px;">
                        <!-- Carregando via JS -->
                        <div class="p-8 text-center text-gray-400">
                            <i class="fas fa-spinner fa-spin text-4xl mb-4"></i>
                            <p>Carregando emails...</p>
                        </div>
                    </div>
                    <div id="pagination" class="p-4 border-t">
                        <!-- Paginação via JS -->
                    </div>
                </div>

                <!-- Visualização de Email -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
                    <div id="emailViewer" class="p-8 text-center text-gray-400">
                        <i class="fas fa-envelope-open text-6xl mb-4"></i>
                        <p class="text-xl">Selecione um email para visualizar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Resposta -->
    <div id="replyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 rounded-t-lg">
                <h3 class="text-2xl font-bold"><i class="fas fa-reply mr-2"></i>Responder Email</h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Para:</label>
                    <p id="replyTo" class="text-gray-600"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Assunto:</label>
                    <p id="replySubject" class="text-gray-600"></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Mensagem:</label>
                    <textarea id="replyBody" rows="8" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
                <div class="flex gap-4">
                    <button onclick="sendReply()" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-3 rounded-lg font-bold hover:from-purple-700 hover:to-purple-800">
                        <i class="fas fa-paper-plane mr-2"></i>Enviar Resposta
                    </button>
                    <button onclick="closeReplyModal()" class="px-6 py-3 border rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentEmail = null;
        let currentPage = 1;

        // Carregar estatísticas
        async function loadStats() {
            try {
                const response = await fetch('api/inbox.php?action=stats');
                const data = await response.json();
                
                if (data.success) {
                    const stats = data.stats;
                    document.getElementById('stats').innerHTML = `
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <div class="text-3xl font-bold text-purple-600">${stats.total || 0}</div>
                            <div class="text-gray-600 mt-1">Total</div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <div class="text-3xl font-bold text-yellow-600">${stats.unread || 0}</div>
                            <div class="text-gray-600 mt-1">Não Lidos</div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <div class="text-3xl font-bold text-green-600">${stats.by_category?.cliente || 0}</div>
                            <div class="text-gray-600 mt-1">Clientes</div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <div class="text-3xl font-bold text-blue-600">${stats.by_category?.suporte || 0}</div>
                            <div class="text-gray-600 mt-1">Suporte</div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <div class="text-3xl font-bold text-red-600">${stats.by_category?.spam || 0}</div>
                            <div class="text-gray-600 mt-1">Spam</div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erro ao carregar stats:', error);
            }
        }

        // Carregar emails
        async function loadEmails(page = 1) {
            currentPage = page;
            const search = document.getElementById('search').value;
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            const params = new URLSearchParams({
                action: 'list',
                page: page,
                limit: 20,
                ...(search && { search }),
                ...(category && { category }),
                ...(status && { status })
            });
            
            try {
                const response = await fetch(`api/inbox.php?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    renderEmailList(data.emails);
                    renderPagination(data.pagination);
                }
            } catch (error) {
                console.error('Erro ao carregar emails:', error);
            }
        }

        // Renderizar lista
        function renderEmailList(emails) {
            const list = document.getElementById('emailList');
            
            if (emails.length === 0) {
                list.innerHTML = '<div class="p-8 text-center text-gray-400">Nenhum email encontrado</div>';
                return;
            }
            
            list.innerHTML = emails.map(email => `
                <div class="email-item ${email.status} p-4 border-b cursor-pointer hover:bg-gray-50" onclick="viewEmail(${email.id})">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">${email.from_name}</div>
                            <div class="text-xs text-gray-500">${email.from_email}</div>
                        </div>
                        <span class="badge badge-${email.category}">${email.category}</span>
                    </div>
                    <div class="text-sm text-gray-700 mb-1">${email.subject || '(Sem assunto)'}</div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">${formatDate(email.received_at)}</span>
                        <span class="badge badge-${email.status}">${translateStatus(email.status)}</span>
                    </div>
                </div>
            `).join('');
        }

        // Renderizar paginação
        function renderPagination(pagination) {
            const pag = document.getElementById('pagination');
            const { page, pages, total } = pagination;
            
            let html = `<div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">Total: ${total} emails</div>
                <div class="flex gap-2">`;
            
            if (page > 1) {
                html += `<button onclick="loadEmails(${page - 1})" class="px-3 py-1 border rounded hover:bg-gray-50">Anterior</button>`;
            }
            
            html += `<span class="px-3 py-1">Página ${page} de ${pages}</span>`;
            
            if (page < pages) {
                html += `<button onclick="loadEmails(${page + 1})" class="px-3 py-1 border rounded hover:bg-gray-50">Próxima</button>`;
            }
            
            html += `</div></div>`;
            pag.innerHTML = html;
        }

        // Visualizar email
        async function viewEmail(id) {
            try {
                const response = await fetch(`api/inbox.php?action=view&id=${id}`);
                const data = await response.json();
                
                if (data.success) {
                    currentEmail = data.email;
                    renderEmailViewer(data.email);
                    loadStats(); // Atualizar contador de não lidos
                    loadEmails(currentPage); // Atualizar lista
                }
            } catch (error) {
                console.error('Erro ao visualizar email:', error);
            }
        }

        // Renderizar visualização
        function renderEmailViewer(email) {
            const viewer = document.getElementById('emailViewer');
            
            const attachments = email.attachments && email.attachments.length > 0 ? `
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-bold mb-2"><i class="fas fa-paperclip mr-2"></i>Anexos (${email.attachments.length})</h4>
                    <div class="space-y-2">
                        ${email.attachments.map(att => `
                            <a href="${att.path}" target="_blank" class="flex items-center gap-2 text-purple-600 hover:text-purple-800">
                                <i class="fas fa-file"></i>
                                <span>${att.name} (${formatSize(att.size)})</span>
                            </a>
                        `).join('')}
                    </div>
                </div>
            ` : '';
            
            viewer.innerHTML = `
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">${email.subject || '(Sem assunto)'}</h2>
                            <div class="space-y-1">
                                <div><strong>De:</strong> ${email.from_name} &lt;${email.from_email}&gt;</div>
                                <div><strong>Data:</strong> ${formatDate(email.received_at)}</div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <span class="badge badge-${email.category}">${email.category}</span>
                            <span class="badge badge-${email.status}">${translateStatus(email.status)}</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="email-body prose max-w-none">
                        ${email.body}
                    </div>
                    
                    ${attachments}
                    
                    <div class="flex gap-4 mt-6 pt-6 border-t">
                        <button onclick="openReplyModal()" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-3 rounded-lg font-bold hover:from-purple-700 hover:to-purple-800">
                            <i class="fas fa-reply mr-2"></i>Responder
                        </button>
                        <button onclick="deleteEmail(${email.id})" class="px-6 py-3 border border-red-500 text-red-500 rounded-lg hover:bg-red-50">
                            <i class="fas fa-trash mr-2"></i>Deletar
                        </button>
                    </div>
                    
                    ${email.status === 'replied' && email.replied_at ? `
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Respondido em ${formatDate(email.replied_at)}
                        </div>
                    ` : ''}
                </div>
            `;
        }

        // Buscar novos emails
        async function fetchEmails() {
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Buscando...';
            
            try {
                const response = await fetch('api/inbox.php?action=fetch');
                const data = await response.json();
                
                if (data.success) {
                    alert(`${data.result.processed} de ${data.result.total} emails processados!`);
                    loadStats();
                    loadEmails(currentPage);
                } else {
                    alert('Erro: ' + data.error);
                }
            } catch (error) {
                alert('Erro ao buscar emails: ' + error.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Buscar Novos';
            }
        }

        // Modal de resposta
        function openReplyModal() {
            if (!currentEmail) return;
            
            document.getElementById('replyTo').textContent = `${currentEmail.from_name} <${currentEmail.from_email}>`;
            document.getElementById('replySubject').textContent = `Re: ${currentEmail.subject}`;
            document.getElementById('replyBody').value = '';
            document.getElementById('replyModal').classList.remove('hidden');
        }

        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
        }

        // Enviar resposta
        async function sendReply() {
            const body = document.getElementById('replyBody').value;
            
            if (!body.trim()) {
                alert('Digite uma mensagem');
                return;
            }
            
            try {
                const response = await fetch('api/inbox.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'reply',
                        email_id: currentEmail.id,
                        reply_body: body
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Resposta enviada com sucesso!');
                    closeReplyModal();
                    viewEmail(currentEmail.id); // Recarregar email
                    loadStats();
                } else {
                    alert('Erro: ' + data.error);
                }
            } catch (error) {
                alert('Erro ao enviar resposta: ' + error.message);
            }
        }

        // Deletar email
        async function deleteEmail(id) {
            if (!confirm('Tem certeza que deseja deletar este email?')) return;
            
            try {
                const response = await fetch('api/inbox.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'delete',
                        email_id: id
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Email deletado!');
                    document.getElementById('emailViewer').innerHTML = '<div class="p-8 text-center text-gray-400"><i class="fas fa-envelope-open text-6xl mb-4"></i><p class="text-xl">Selecione um email para visualizar</p></div>';
                    loadStats();
                    loadEmails(currentPage);
                } else {
                    alert('Erro: ' + data.error);
                }
            } catch (error) {
                alert('Erro ao deletar: ' + error.message);
            }
        }

        // Utilitários
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('pt-BR');
        }

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function translateStatus(status) {
            const map = {
                'unread': 'Não lido',
                'read': 'Lido',
                'replied': 'Respondido',
                'archived': 'Arquivado'
            };
            return map[status] || status;
        }

        // Inicializar
        loadStats();
        loadEmails();
        
        // Auto-refresh a cada 5 minutos
        setInterval(() => {
            loadStats();
            if (document.getElementById('statusFilter').value === 'unread') {
                loadEmails(currentPage);
            }
        }, 5 * 60 * 1000);
    </script>
</body>
</html>
