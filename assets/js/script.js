// Função para alternar entre os formulários
function updateForm() {
    const qrType = document.getElementById('qr_type').value;
    const formTypes = document.querySelectorAll('.form-type');
    
    formTypes.forEach(form => form.classList.add('hidden'));
    
    const selectedForm = document.getElementById(qrType + '_form');
    if (selectedForm) {
        selectedForm.classList.remove('hidden');
    }
}

// Função para formatar telefone brasileiro
function formatPhone(value) {
    // Remove tudo que não é número
    const numbers = value.replace(/\D/g, '');
    
    // Limita a 11 dígitos
    const limited = numbers.slice(0, 11);
    
    // Aplica a formatação
    if (limited.length === 0) return '';
    if (limited.length <= 2) return `(${limited}`;
    if (limited.length <= 6) return `(${limited.slice(0, 2)}) ${limited.slice(2)}`;
    if (limited.length <= 10) return `(${limited.slice(0, 2)}) ${limited.slice(2, 6)}-${limited.slice(6)}`;
    return `(${limited.slice(0, 2)}) ${limited.slice(2, 7)}-${limited.slice(7)}`;
}

// Função para alternar tema
function toggleTheme() {
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle.querySelector('i');
    const isDark = body.classList.contains('dark-theme');
    
    if (isDark) {
        // Mudar para tema claro
        body.classList.remove('dark-theme');
        body.classList.remove('bg-gradient-to-br', 'from-gray-900', 'via-blue-900', 'to-black');
        body.classList.add('bg-gradient-to-br', 'from-blue-50', 'via-white', 'to-blue-100');
        
        // Atualizar ícone
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        
        // Salvar preferência
        localStorage.setItem('theme', 'light');
        
        // Atualizar elementos
        updateThemeElements(false);
    } else {
        // Mudar para tema escuro
        body.classList.add('dark-theme');
        body.classList.remove('bg-gradient-to-br', 'from-blue-50', 'via-white', 'to-blue-100');
        body.classList.add('bg-gradient-to-br', 'from-gray-900', 'via-blue-900', 'to-black');
        
        // Atualizar ícone
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        
        // Salvar preferência
        localStorage.setItem('theme', 'dark');
        
        // Atualizar elementos
        updateThemeElements(true);
    }
}

// Função para atualizar elementos do tema
function updateThemeElements(isDark) {
    const labels = document.querySelectorAll('label');
    const h1 = document.querySelector('h1');
    const h2 = document.querySelector('h2');
    const subtitle = document.getElementById('subtitle');
    const cards = document.querySelectorAll('#mainCard, #sideCard');
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="url"], textarea, select');
    
    if (isDark) {
        // Tema escuro
        labels.forEach(label => {
            label.classList.remove('text-gray-700');
            label.classList.add('text-white');
        });
        
        if (h1) {
            h1.classList.remove('text-gray-800');
            h1.classList.add('text-white');
        }
        
        if (h2) {
            h2.classList.remove('text-gray-800');
            h2.classList.add('text-white');
        }
        
        if (subtitle) {
            subtitle.classList.remove('text-gray-600');
            subtitle.classList.add('text-blue-200');
        }
        
        cards.forEach(card => {
            card.classList.remove('bg-white', 'border-gray-200');
            card.classList.add('bg-white/10', 'backdrop-blur-lg', 'border-blue-500/30');
        });
        
        inputs.forEach(input => {
            input.classList.remove('bg-gray-100', 'text-gray-800', 'border-gray-300', 'focus:border-blue-300');
            input.classList.add('bg-gray-800', 'text-white', 'border-blue-500/50', 'focus:border-blue-400');
        });
    } else {
        // Tema claro
        labels.forEach(label => {
            label.classList.remove('text-white');
            label.classList.add('text-gray-700');
        });
        
        if (h1) {
            h1.classList.remove('text-white');
            h1.classList.add('text-gray-800');
        }
        
        if (h2) {
            h2.classList.remove('text-white');
            h2.classList.add('text-gray-800');
        }
        
        if (subtitle) {
            subtitle.classList.remove('text-blue-200');
            subtitle.classList.add('text-gray-600');
        }
        
        cards.forEach(card => {
            card.classList.remove('bg-white/10', 'backdrop-blur-lg', 'border-blue-500/30');
            card.classList.add('bg-white', 'border-gray-200');
        });
        
        inputs.forEach(input => {
            input.classList.remove('bg-gray-800', 'text-white', 'border-blue-500/50', 'focus:border-blue-400');
            input.classList.add('bg-gray-100', 'text-gray-800', 'border-gray-300', 'focus:border-blue-300');
        });
    }
}

// Inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Verificar tema salvo
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        toggleTheme(); // Inicia no tema claro se estava salvo
    }
    
    // Toggle de tema
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    // Sincronizar color pickers
    const fgColor = document.getElementById('fg_color');
    const fgColorText = document.getElementById('fg_color_text');
    const bgColor = document.getElementById('bg_color');
    const bgColorText = document.getElementById('bg_color_text');
    const margin = document.getElementById('margin');
    const marginValue = document.getElementById('margin_value');
    
    // Cor do QR Code
    if (fgColor && fgColorText) {
        fgColor.addEventListener('input', function() {
            fgColorText.value = this.value;
        });
        
        fgColorText.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                fgColor.value = this.value;
            }
        });
    }
    
    // Cor de fundo
    if (bgColor && bgColorText) {
        bgColor.addEventListener('input', function() {
            bgColorText.value = this.value;
        });
        
        bgColorText.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                bgColor.value = this.value;
            }
        });
    }
    
    // Margem
    if (margin && marginValue) {
        margin.addEventListener('input', function() {
            marginValue.textContent = this.value;
        });
    }
    
    // Máscara de telefone para todos os campos
    const phoneInputs = document.querySelectorAll('#phone_input, #sms_phone_input, #vcard_phone_input');
    
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            const cursorPosition = this.selectionStart;
            const oldValue = this.value;
            const formatted = formatPhone(this.value);
            
            this.value = formatted;
            
            // Ajustar posição do cursor
            if (formatted.length > oldValue.length) {
                this.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
            } else {
                this.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
        
        // Bloquear caracteres não numéricos via teclado
        input.addEventListener('keypress', function(e) {
            if (!/\d/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
                e.preventDefault();
            }
        });
    });
    
    // Listener para mudança de tipo de QR Code
    const qrTypeSelect = document.getElementById('qr_type');
    if (qrTypeSelect) {
        qrTypeSelect.addEventListener('change', updateForm);
    }
    
    // Inicializar formulário
    updateForm();
});
