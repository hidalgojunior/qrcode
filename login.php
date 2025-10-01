<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DevMenthors</title>
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center relative overflow-hidden">
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
                <p class="text-gray-600 mt-2">Entre na sua conta</p>
            </div>

            <!-- Mensagens -->
            <div id="message" class="hidden mb-4 p-4 rounded-lg"></div>

            <!-- Form de Login -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form id="loginForm" method="POST">
                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="seu@email.com">
                    </div>

                    <!-- Senha -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Senha
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i id="toggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Lembrar-me e Esqueci senha -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="mr-2 rounded">
                            <span class="text-sm text-gray-600">Lembrar-me</span>
                        </label>
                        <a href="forgot-password.php" class="text-sm text-blue-600 hover:text-blue-800">
                            Esqueceu a senha?
                        </a>
                    </div>

                    <!-- Botão Entrar -->
                    <button type="submit" 
                            class="w-full gradient-bg text-white py-3 rounded-lg font-bold hover:shadow-lg transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Entrar
                    </button>
                </form>

                <!-- Divisor -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Ou continue com</span>
                    </div>
                </div>

                <!-- Social Login -->
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

                <!-- Link para Registro -->
                <p class="text-center text-sm text-gray-600 mt-6">
                    Não tem uma conta?
                    <a href="register.php" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Criar conta grátis
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
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
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
            
            // Auto-hide após 5 segundos
            setTimeout(() => {
                messageDiv.classList.add('hidden');
            }, 5000);
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const button = e.target.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Entrando...';
            
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('Login realizado com sucesso! Redirecionando...', 'success');
                    setTimeout(() => {
                        window.location.href = 'dashboard/';
                    }, 1000);
                } else {
                    showMessage(data.error || 'Erro ao fazer login');
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
