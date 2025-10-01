<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - DevMenthors</title>
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

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            animation: blob 7s infinite;
        }

        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center relative overflow-hidden py-8">
    <!-- Blobs animados -->
    <div class="blob w-96 h-96 bg-blue-400 top-0 left-0"></div>
    <div class="blob w-80 h-80 bg-purple-400 bottom-0 right-0"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-md mx-auto">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="home.php" class="inline-flex items-center justify-center">
                    <i class="fas fa-qrcode text-5xl gradient-text mr-3"></i>
                    <span class="text-4xl font-bold gradient-text">DevMenthors</span>
                </a>
                <p class="text-gray-600 mt-2">Crie sua conta gratuitamente</p>
            </div>

            <!-- Mensagens -->
            <div id="message" class="hidden mb-4 p-4 rounded-lg"></div>

            <!-- Form de Cadastro -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form id="registerForm" method="POST">
                    <!-- Nome -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Nome Completo
                        </label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Seu nome completo">
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="seu@email.com">
                    </div>

                    <!-- Telefone -->
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2"></i>Telefone (opcional)
                        </label>
                        <input type="tel" id="phone" name="phone"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="(00) 00000-0000"
                               maxlength="15">
                    </div>

                    <!-- Senha -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Senha
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                   placeholder="Mínimo 6 caracteres"
                                   minlength="6">
                            <button type="button" onclick="togglePassword('password')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i id="toggleIconPassword" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <div id="passwordStrength" class="password-strength bg-gray-200"></div>
                            <p id="passwordStrengthText" class="text-xs text-gray-500 mt-1"></p>
                        </div>
                    </div>

                    <!-- Confirmar Senha -->
                    <div class="mb-6">
                        <label for="password_confirm" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Confirmar Senha
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirm" name="password_confirm" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                   placeholder="Digite a senha novamente"
                                   minlength="6">
                            <button type="button" onclick="togglePassword('password_confirm')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i id="toggleIconPasswordConfirm" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Termos -->
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" name="terms" required class="mt-1 mr-2 rounded">
                            <span class="text-sm text-gray-600">
                                Eu concordo com os 
                                <a href="#" class="text-blue-600 hover:text-blue-800">Termos de Uso</a> 
                                e 
                                <a href="#" class="text-blue-600 hover:text-blue-800">Política de Privacidade</a>
                            </span>
                        </label>
                    </div>

                    <!-- Botão Cadastrar -->
                    <button type="submit" 
                            class="w-full gradient-bg text-white py-3 rounded-lg font-bold hover:shadow-lg transition">
                        <i class="fas fa-user-plus mr-2"></i>Criar Conta
                    </button>
                </form>

                <!-- Divisor -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Ou cadastre-se com</span>
                    </div>
                </div>

                <!-- Social Register -->
                <div class="grid grid-cols-2 gap-4">
                    <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fab fa-google text-red-500 mr-2"></i>
                        Google
                    </button>
                    <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fab fa-facebook-f text-blue-600 mr-2"></i>
                        Facebook
                    </button>
                </div>

                <!-- Link para Login -->
                <p class="text-center text-sm text-gray-600 mt-6">
                    Já tem uma conta?
                    <a href="login.php" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Fazer login
                    </a>
                </p>
            </div>

            <!-- Link para Home -->
            <div class="text-center mt-6">
                <a href="home.php" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar para home
                </a>
            </div>
        </div>
    </div>

    <script>
        // Máscara de telefone
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            if (value.length === 0) {
                e.target.value = '';
            } else if (value.length <= 2) {
                e.target.value = `(${value}`;
            } else if (value.length <= 6) {
                e.target.value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
            } else if (value.length <= 10) {
                e.target.value = `(${value.substring(0, 2)}) ${value.substring(2, 6)}-${value.substring(6)}`;
            } else {
                e.target.value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7)}`;
            }
        });

        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById('toggleIcon' + fieldId.charAt(0).toUpperCase() + fieldId.slice(1).replace('_', ''));
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('passwordStrengthText');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;
            
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
            const texts = ['Muito fraca', 'Fraca', 'Média', 'Forte', 'Muito forte'];
            const widths = ['25%', '50%', '75%', '100%'];
            
            strengthBar.className = `password-strength ${colors[Math.min(strength - 1, 3)]}`;
            strengthBar.style.width = widths[Math.min(strength - 1, 3)] || '0%';
            strengthText.textContent = strength > 0 ? `Força da senha: ${texts[strength - 1]}` : '';
        });

        function showMessage(message, type = 'error') {
            const messageDiv = document.getElementById('message');
            messageDiv.className = `mb-4 p-4 rounded-lg ${type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'}`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            messageDiv.classList.remove('hidden');
            
            setTimeout(() => {
                messageDiv.classList.add('hidden');
            }, 5000);
        }

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (password !== passwordConfirm) {
                showMessage('As senhas não coincidem');
                return;
            }
            
            const formData = new FormData(e.target);
            const button = e.target.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Criando conta...';
            
            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('Conta criada com sucesso! Redirecionando...', 'success');
                    setTimeout(() => {
                        window.location.href = 'dashboard/';
                    }, 2000);
                } else {
                    showMessage(data.error || 'Erro ao criar conta');
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            } catch (error) {
                showMessage('Erro ao conectar com o servidor');
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    </script>
</body>
</html>
