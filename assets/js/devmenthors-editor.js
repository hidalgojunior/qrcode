// Contador de widgets
let widgetCount = 0;
let slugAvailable = false;
let slugCheckTimeout = null;
let avatarDataUrl = null; // Armazena a imagem em base64

// Função para upload de avatar
function setupAvatarUpload() {
    const uploadInput = document.getElementById('avatarUpload');
    const avatarUrlInput = document.getElementById('avatar');
    const previewDiv = document.getElementById('avatarPreview');
    const previewImg = document.getElementById('avatarPreviewImg');
    
    // Upload de arquivo
    uploadInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamanho (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Arquivo muito grande! Máximo 2MB.');
                return;
            }
            
            // Validar tipo
            if (!file.type.startsWith('image/')) {
                alert('Por favor, selecione uma imagem válida.');
                return;
            }
            
            // Ler arquivo
            const reader = new FileReader();
            reader.onload = function(event) {
                avatarDataUrl = event.target.result;
                previewImg.src = avatarDataUrl;
                previewDiv.classList.remove('hidden');
                avatarUrlInput.value = ''; // Limpa URL se tinha
                updatePreview();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // URL de avatar
    avatarUrlInput.addEventListener('input', function() {
        if (this.value) {
            avatarDataUrl = null; // Limpa upload se tinha
            uploadInput.value = ''; // Limpa input file
            previewImg.src = this.value;
            previewDiv.classList.remove('hidden');
        } else if (!avatarDataUrl) {
            previewDiv.classList.add('hidden');
        }
        updatePreview();
    });
    
    // Drag and drop
    const dropZone = uploadInput.parentElement;
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.querySelector('div').classList.add('border-yellow-400', 'bg-white/10');
        });
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.querySelector('div').classList.remove('border-yellow-400', 'bg-white/10');
        });
    });
    
    dropZone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        uploadInput.files = files;
        
        // Trigger change event
        const event = new Event('change', { bubbles: true });
        uploadInput.dispatchEvent(event);
    });
}

// Função para remover avatar
function removeAvatar() {
    avatarDataUrl = null;
    document.getElementById('avatarUpload').value = '';
    document.getElementById('avatar').value = '';
    document.getElementById('avatarPreview').classList.add('hidden');
    updatePreview();
}

// Função para validar e formatar slug
function formatSlug(value) {
    return value
        .toLowerCase()
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/--+/g, '-')
        .replace(/^-|-$/g, '');
}

// Função para verificar disponibilidade do slug
async function checkSlugAvailability(slug) {
    if (!slug || slug.length < 3) {
        document.getElementById('slugStatus').innerHTML = 
            '<span class="text-yellow-400"><i class="fas fa-exclamation-triangle mr-1"></i>Mínimo 3 caracteres</span>';
        slugAvailable = false;
        return;
    }
    
    try {
        const response = await fetch(`check-slug.php?slug=${encodeURIComponent(slug)}`);
        const result = await response.json();
        
        const statusDiv = document.getElementById('slugStatus');
        if (result.available) {
            statusDiv.innerHTML = 
                '<span class="text-green-400"><i class="fas fa-check-circle mr-1"></i>URL disponível!</span>';
            slugAvailable = true;
        } else {
            statusDiv.innerHTML = 
                '<span class="text-red-400"><i class="fas fa-times-circle mr-1"></i>URL já está em uso. Tente outra.</span>';
            slugAvailable = false;
        }
    } catch (error) {
        console.error('Erro ao verificar slug:', error);
        slugAvailable = true; // Permite continuar em caso de erro
    }
}

// Mostrar/esconder menu de widgets
document.addEventListener('click', function(e) {
    const menu = document.getElementById('widgetMenu');
    const btn = document.getElementById('addWidgetBtn');
    
    if (btn && btn.contains(e.target)) {
        menu.classList.toggle('hidden');
    } else if (menu && !menu.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

// Função para adicionar widget
function addWidget(type) {
    widgetCount++;
    const container = document.getElementById('widgetsContainer');
    const widgetDiv = document.createElement('div');
    widgetDiv.className = 'border border-blue-500/30 rounded-lg p-4 space-y-3';
    widgetDiv.setAttribute('data-widget-id', widgetCount);
    widgetDiv.setAttribute('data-widget-type', type);
    
    let widgetHTML = '';
    
    switch(type) {
        case 'link':
            widgetHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold"><i class="fas fa-link mr-2 text-blue-400"></i>Link/Botão</span>
                    <button type="button" onclick="removeWidget(${widgetCount})" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="text" placeholder="Título do botão" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="title">
                <input type="url" placeholder="URL do link" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="url">
                <div class="flex gap-2">
                    <input type="color" value="#667eea"
                           class="w-16 h-10 rounded border border-blue-500/50 cursor-pointer"
                           data-widget-field="color">
                    <input type="text" placeholder="Ícone (ex: fas fa-link)" 
                           class="flex-1 px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                           data-widget-field="icon">
                </div>
            `;
            break;
            
        case 'pix':
            widgetHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold"><i class="fas fa-money-bill mr-2 text-green-400"></i>Chave PIX</span>
                    <button type="button" onclick="removeWidget(${widgetCount})" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="text" placeholder="Título (ex: Fazer uma doação)" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="title">
                <select class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                        data-widget-field="pix_type">
                    <option value="cpf">CPF</option>
                    <option value="cnpj">CNPJ</option>
                    <option value="email">Email</option>
                    <option value="phone">Telefone</option>
                    <option value="random">Chave Aleatória</option>
                </select>
                <input type="text" placeholder="Chave PIX" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="pix_key">
                <input type="text" placeholder="Nome do beneficiário" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="pix_name">
            `;
            break;
            
        case 'gallery':
            widgetHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold"><i class="fas fa-images mr-2 text-purple-400"></i>Galeria de Fotos</span>
                    <button type="button" onclick="removeWidget(${widgetCount})" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="text" placeholder="Título da galeria" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="title">
                <textarea placeholder="URLs das imagens (uma por linha)" rows="4"
                          class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                          data-widget-field="images"></textarea>
                <p class="text-xs text-blue-200">Cole uma URL por linha</p>
            `;
            break;
            
        case 'music':
            widgetHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold"><i class="fas fa-music mr-2 text-pink-400"></i>Player de Música</span>
                    <button type="button" onclick="removeWidget(${widgetCount})" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="text" placeholder="Título da música" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="title">
                <input type="text" placeholder="Artista" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="artist">
                <input type="url" placeholder="Link do Spotify/YouTube/SoundCloud" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="music_url">
                <input type="url" placeholder="URL da capa do álbum (opcional)" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="cover">
            `;
            break;
            
        case 'video':
            widgetHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold"><i class="fas fa-video mr-2 text-red-400"></i>Vídeo YouTube</span>
                    <button type="button" onclick="removeWidget(${widgetCount})" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="text" placeholder="Título do vídeo" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="title">
                <input type="text" placeholder="ID do vídeo do YouTube (ex: dQw4w9WgXcQ)" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="video_id">
                <p class="text-xs text-blue-200">Cole apenas o ID do vídeo (parte depois de youtube.com/watch?v=)</p>
            `;
            break;
            
        case 'text':
            widgetHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold"><i class="fas fa-align-left mr-2 text-yellow-400"></i>Bloco de Texto</span>
                    <button type="button" onclick="removeWidget(${widgetCount})" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="text" placeholder="Título (opcional)" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="title">
                <textarea placeholder="Seu texto aqui..." rows="4"
                          class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                          data-widget-field="content"></textarea>
            `;
            break;
            
        case 'location':
            widgetHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold"><i class="fas fa-map-marker-alt mr-2 text-orange-400"></i>Localização</span>
                    <button type="button" onclick="removeWidget(${widgetCount})" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="text" placeholder="Nome do local" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="title">
                <input type="text" placeholder="Endereço completo" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="address">
                <input type="url" placeholder="Link do Google Maps" 
                       class="w-full px-4 py-2 bg-gray-800 text-white border border-blue-500/50 rounded-lg focus:outline-none focus:border-blue-400"
                       data-widget-field="maps_url">
            `;
            break;
    }
    
    widgetDiv.innerHTML = widgetHTML;
    container.appendChild(widgetDiv);
    
    // Adicionar event listeners
    widgetDiv.querySelectorAll('input, textarea, select').forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });
    
    // Fechar menu
    document.getElementById('widgetMenu').classList.add('hidden');
    
    updatePreview();
}

// Função para remover widget
function removeWidget(id) {
    const widgetDiv = document.querySelector(`[data-widget-id="${id}"]`);
    if (widgetDiv) {
        widgetDiv.remove();
        updatePreview();
    }
}

// Função para atualizar preview
function updatePreview() {
    const previewContentMobile = document.getElementById('previewContentMobile');
    const previewContentDesktop = document.getElementById('previewContentDesktop');
    
    // Coletar dados
    const name = document.getElementById('name').value || 'Seu Nome';
    const title = document.getElementById('title').value;
    const bio = document.getElementById('bio').value;
    const avatarUrl = document.getElementById('avatar').value;
    const currentAvatar = avatarDataUrl || avatarUrl; // Prioriza upload
    
    // Atualizar slug preview
    const slug = document.getElementById('slug').value || 'seu-slug';
    document.getElementById('slugPreview').textContent = slug;
    if (document.getElementById('slugPreviewDesktop')) {
        document.getElementById('slugPreviewDesktop').textContent = slug;
    }
    
    // Coletar redes sociais
    const socialInputs = document.querySelectorAll('[data-social]');
    const socials = [];
    socialInputs.forEach(input => {
        if (input.value) {
            socials.push({
                name: input.getAttribute('data-social'),
                icon: input.getAttribute('data-icon'),
                url: input.value
            });
        }
    });
    
    // Coletar widgets
    const widgets = [];
    const widgetDivs = document.querySelectorAll('[data-widget-id]');
    widgetDivs.forEach(div => {
        const type = div.getAttribute('data-widget-type');
        const fields = div.querySelectorAll('[data-widget-field]');
        const widgetData = { type };
        
        fields.forEach(field => {
            const fieldName = field.getAttribute('data-widget-field');
            widgetData[fieldName] = field.value;
        });
        
        widgets.push(widgetData);
    });
    
    // Gerar HTML do preview (Mobile - Compacto)
    let htmlMobile = '<div class="text-center">';
    
    // Avatar
    if (currentAvatar) {
        htmlMobile += `<img src="${currentAvatar}" alt="Avatar" class="w-20 h-20 rounded-full mx-auto mb-3 object-cover border-4 border-gray-100 shadow-lg">`;
    } else {
        htmlMobile += '<div class="w-20 h-20 rounded-full mx-auto mb-3 bg-gray-200 flex items-center justify-center shadow-lg"><i class="fas fa-user text-2xl text-gray-400"></i></div>';
    }
    
    // Nome e título
    htmlMobile += `<h2 class="text-xl font-bold text-gray-800 mb-1">${name}</h2>`;
    if (title) {
        htmlMobile += `<p class="text-gray-600 text-sm mb-2">${title}</p>`;
    }
    if (bio) {
        htmlMobile += `<p class="text-xs text-gray-600 mb-3 line-clamp-2">${bio}</p>`;
    }
    
    // Redes sociais
    if (socials.length > 0) {
        htmlMobile += '<div class="flex justify-center gap-2 mb-4">';
        socials.forEach(social => {
            htmlMobile += `<div class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 text-sm"><i class="${social.icon}"></i></div>`;
        });
        htmlMobile += '</div>';
    }
    
    htmlMobile += '</div>';
    
    // Widgets (Mobile)
    if (widgets.length > 0) {
        htmlMobile += '<div class="space-y-2 mt-4">';
        widgets.forEach(widget => {
            if (widget.type === 'link' && widget.title) {
                htmlMobile += `<div class="bg-gray-100 rounded-lg p-2 text-center text-sm font-semibold text-gray-700">${widget.title}</div>`;
            }
        });
        htmlMobile += '</div>';
    }
    
    previewContentMobile.innerHTML = htmlMobile;
    
    // Gerar HTML do preview (Desktop - Detalhado)
    let htmlDesktop = '<div class="text-center">';
    
    // Avatar
    if (currentAvatar) {
        htmlDesktop += `<img src="${currentAvatar}" alt="Avatar" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-4 border-gray-100 shadow-xl">`;
    } else {
        htmlDesktop += '<div class="w-32 h-32 rounded-full mx-auto mb-4 bg-gray-200 flex items-center justify-center shadow-xl"><i class="fas fa-user text-4xl text-gray-400"></i></div>';
    }
    
    // Nome e título
    htmlDesktop += `<h2 class="text-3xl font-bold text-gray-800 mb-2">${name}</h2>`;
    if (title) {
        htmlDesktop += `<p class="text-gray-600 text-lg mb-3">${title}</p>`;
    }
    if (bio) {
        htmlDesktop += `<p class="text-gray-600 mb-6 max-w-2xl mx-auto">${bio}</p>`;
    }
    
    // Redes sociais
    if (socials.length > 0) {
        htmlDesktop += '<div class="flex justify-center gap-4 mb-8">';
        socials.forEach(social => {
            htmlDesktop += `<div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition cursor-pointer"><i class="${social.icon}"></i></div>`;
        });
        htmlDesktop += '</div>';
    }
    
    htmlDesktop += '</div>';
    
    // Widgets (Desktop)
    if (widgets.length > 0) {
        htmlDesktop += '<div class="space-y-3 mt-6">';
        widgets.forEach(widget => {
            if (widget.type === 'link' && widget.title) {
                const color = widget.color || '#667eea';
                htmlDesktop += `<div class="rounded-xl p-4 text-center font-semibold text-white shadow-lg" style="background-color: ${color};">${widget.title}</div>`;
            } else if (widget.type === 'text' && widget.content) {
                htmlDesktop += `<div class="bg-gray-50 rounded-xl p-4"><p class="text-gray-700">${widget.content}</p></div>`;
            }
        });
        htmlDesktop += '</div>';
    }
    
    previewContentDesktop.innerHTML = htmlDesktop;
}

// Função para renderizar preview do widget
function renderWidgetPreview(widget) {
    switch(widget.type) {
        case 'link':
            if (widget.title && widget.url) {
                const icon = widget.icon ? `<i class="${widget.icon} mr-2"></i>` : '';
                return `
                    <div class="w-full py-3 px-4 rounded-lg font-semibold text-center text-sm text-white" 
                         style="background-color: ${widget.color || '#667eea'}">
                        ${icon}${widget.title}
                    </div>
                `;
            }
            break;
            
        case 'pix':
            if (widget.pix_key) {
                return `
                    <div class="bg-green-50 border-2 border-green-500 rounded-lg p-4">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <i class="fas fa-money-bill text-2xl text-green-600"></i>
                            <span class="font-bold text-gray-800">${widget.title || 'PIX'}</span>
                        </div>
                        <p class="text-xs text-gray-600 text-center">${widget.pix_name || 'Chave PIX disponível'}</p>
                    </div>
                `;
            }
            break;
            
        case 'gallery':
            if (widget.images) {
                const images = widget.images.split('\n').filter(url => url.trim());
                if (images.length > 0) {
                    return `
                        <div class="border rounded-lg p-3">
                            ${widget.title ? `<h3 class="font-semibold text-gray-800 mb-2 text-sm">${widget.title}</h3>` : ''}
                            <div class="grid grid-cols-3 gap-2">
                                ${images.slice(0, 6).map(url => `<div class="aspect-square bg-gray-200 rounded"><img src="${url}" class="w-full h-full object-cover rounded" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect fill=\'%23ddd\' width=\'100\' height=\'100\'/%3E%3C/svg%3E'"></div>`).join('')}
                            </div>
                        </div>
                    `;
                }
            }
            break;
            
        case 'music':
            if (widget.title) {
                return `
                    <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg p-4 text-white">
                        <div class="flex items-center gap-3">
                            ${widget.cover ? `<img src="${widget.cover}" class="w-12 h-12 rounded">` : `<div class="w-12 h-12 bg-white/20 rounded flex items-center justify-center"><i class="fas fa-music"></i></div>`}
                            <div class="flex-1">
                                <p class="font-semibold text-sm">${widget.title}</p>
                                ${widget.artist ? `<p class="text-xs opacity-80">${widget.artist}</p>` : ''}
                            </div>
                            <i class="fas fa-play-circle text-2xl"></i>
                        </div>
                    </div>
                `;
            }
            break;
            
        case 'video':
            if (widget.video_id) {
                return `
                    <div class="border rounded-lg overflow-hidden">
                        ${widget.title ? `<div class="p-2 bg-gray-100"><p class="font-semibold text-sm text-gray-800">${widget.title}</p></div>` : ''}
                        <div class="aspect-video bg-gray-900 flex items-center justify-center">
                            <i class="fas fa-play-circle text-5xl text-white opacity-75"></i>
                        </div>
                    </div>
                `;
            }
            break;
            
        case 'text':
            if (widget.content) {
                return `
                    <div class="bg-gray-50 border rounded-lg p-4">
                        ${widget.title ? `<h3 class="font-semibold text-gray-800 mb-2">${widget.title}</h3>` : ''}
                        <p class="text-sm text-gray-600">${widget.content.replace(/\n/g, '<br>')}</p>
                    </div>
                `;
            }
            break;
            
        case 'location':
            if (widget.address) {
                return `
                    <div class="border border-orange-500 bg-orange-50 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-2xl text-orange-600"></i>
                            <div class="flex-1">
                                ${widget.title ? `<p class="font-semibold text-gray-800">${widget.title}</p>` : ''}
                                <p class="text-sm text-gray-600">${widget.address}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            break;
    }
    return '';
}

// Função para coletar dados do formulário
function collectFormData() {
    const data = {
        slug: document.getElementById('slug').value,
        name: document.getElementById('name').value,
        title: document.getElementById('title').value,
        bio: document.getElementById('bio').value,
        avatar: avatarDataUrl || document.getElementById('avatar').value, // Prioriza upload
        theme: {
            gradient_start: document.getElementById('gradient_start').value,
            gradient_end: document.getElementById('gradient_end').value
        },
        social: [],
        widgets: [],
        contact: {
            email: document.getElementById('contact_email').value,
            phone: document.getElementById('contact_phone').value,
            whatsapp: document.getElementById('contact_whatsapp').value
        }
    };
    
    // Coletar redes sociais
    const socialInputs = document.querySelectorAll('[data-social]');
    socialInputs.forEach(input => {
        if (input.value) {
            data.social.push({
                name: input.getAttribute('data-social'),
                icon: input.getAttribute('data-icon'),
                url: input.value
            });
        }
    });
    
    // Coletar widgets
    const widgetDivs = document.querySelectorAll('[data-widget-id]');
    widgetDivs.forEach(div => {
        const type = div.getAttribute('data-widget-type');
        const fields = div.querySelectorAll('[data-widget-field]');
        const widgetData = { type };
        
        fields.forEach(field => {
            const fieldName = field.getAttribute('data-widget-field');
            widgetData[fieldName] = field.value;
        });
        
        data.widgets.push(widgetData);
    });
    
    return data;
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Sugerir slug baseado no nome
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            // Só sugerir se o slug estiver vazio
            if (!slugInput.value) {
                const suggestion = formatSlug(this.value);
                if (suggestion) {
                    slugInput.value = suggestion;
                    checkSlugAvailability(suggestion);
                }
            }
        });
    }
    
    // Validação e formatação do slug
    if (slugInput) {
        slugInput.addEventListener('input', function() {
            this.value = formatSlug(this.value);
            
            // Atualizar preview da URL
            const slugPreview = document.getElementById('slugPreview');
            if (slugPreview) {
                slugPreview.textContent = this.value || 'seu-slug';
            }
            
            // Debounce para verificar disponibilidade
            clearTimeout(slugCheckTimeout);
            slugCheckTimeout = setTimeout(() => {
                if (this.value) {
                    checkSlugAvailability(this.value);
                }
            }, 500);
        });
        
        slugInput.addEventListener('blur', function() {
            if (this.value) {
                checkSlugAvailability(this.value);
            }
        });
    }
    
    // Preview automático
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });
    
    // Setup upload de avatar
    setupAvatarUpload();
    
    // Botão preview manual
    document.getElementById('preview').addEventListener('click', updatePreview);
    
    // Submit do formulário
    document.getElementById('micrositeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const data = collectFormData();
        
        if (!data.slug) {
            alert('Por favor, preencha a URL personalizada!');
            return;
        }
        
        if (!data.name) {
            alert('Por favor, preencha o nome!');
            return;
        }
        
        if (!slugAvailable) {
            alert('A URL escolhida não está disponível. Por favor, escolha outra.');
            return;
        }
        
        // Mostrar loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Criando...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('save-devmenthors.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Redirecionar para página de resultado
                window.location.href = `devmenthors-result.php?id=${result.id}`;
            } else {
                alert('Erro ao criar página: ' + result.message);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao criar página!');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
    
    // Preview inicial
    updatePreview();
});
