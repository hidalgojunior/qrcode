# Guia de Instalação - Área Restrita e Banco de Dados

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx com mod_rewrite
- Extensões PHP: PDO, PDO_MySQL, GD, JSON, mbstring

## 🗄️ Instalação do Banco de Dados

### Passo 1: Criar o banco de dados

```sql
CREATE DATABASE devmenthors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Passo 2: Importar o schema

```bash
mysql -u root -p devmenthors < database/schema.sql
```

Ou pelo phpMyAdmin:
1. Acesse http://localhost/phpmyadmin
2. Crie o banco `devmenthors`
3. Importe o arquivo `database/schema.sql`

### Passo 3: Configurar credenciais

Edite o arquivo `.env` (copie de `.env.example`):

```env
DB_HOST=localhost
DB_NAME=devmenthors
DB_USER=root
DB_PASS=

BASE_URL=http://localhost/QrCode
```

## 🔐 Sistema de Autenticação

O sistema inclui:

- ✅ **Registro de usuários** com validação de email
- ✅ **Login seguro** com hash bcrypt
- ✅ **Sessões** armazenadas no banco de dados
- ✅ **Recuperação de senha** via token
- ✅ **Logs de auditoria** para todas as ações

### Arquivos principais:

- `includes/database.php` - Classe Database e Auth
- `login.php` - Página de login
- `register.php` - Página de cadastro
- `api/login.php` - API de autenticação
- `api/register.php` - API de registro
- `api/logout.php` - API de logout

## 💳 Sistema de Pagamento PIX

### Configuração

1. Edite as configurações do PIX em `settings`:

```sql
UPDATE settings SET value = 'sua-chave@pix.com' WHERE `key` = 'pix_key';
UPDATE settings SET value = 'Seu Nome ou Empresa' WHERE `key` = 'pix_name';
```

2. Para integração real com gateway:

**Mercado Pago:**
```sql
UPDATE settings SET value = 'mercadopago' WHERE `key` = 'payment_gateway';
UPDATE settings SET value = 'APP_USR-xxx-xxx' WHERE `key` = 'mercadopago_public_key';
UPDATE settings SET value = 'APP_USR-xxx-xxx' WHERE `key` = 'mercadopago_access_token';
```

### Fluxo de Pagamento

1. **Usuário escolhe plano** → `dashboard/subscription.php`
2. **Sistema gera PIX** → `api/create-payment.php`
3. **QR Code é exibido** → Modal com código copia e cola
4. **Webhook confirma** → `api/webhook-payment.php` (a implementar)
5. **Assinatura ativada** → Status muda para 'active'

### Arquivos de pagamento:

- `dashboard/subscription.php` - Página de planos
- `api/create-payment.php` - Gera pagamento PIX
- `api/webhook-payment.php` - Recebe confirmações (TODO)

## 📊 Planos Disponíveis

| Plano | Preço | Microsites | Marca d'água |
|-------|-------|------------|--------------|
| Básico | R$ 10 | 1 | Sim |
| Starter | R$ 20 | 1 | Não |
| Pro | R$ 70 | 10 | Não |
| Enterprise | Custom | Ilimitado | Não |

## 🎨 Dashboard

O dashboard inclui:

- 📊 **Estatísticas** (microsites, QR codes, visualizações)
- 📱 **Gerenciamento de microsites**
- 🔍 **Analíticas básicas**
- 👤 **Perfil do usuário**
- ⚙️ **Configurações**
- 💳 **Assinatura e pagamentos**
- 🎫 **Suporte**

### Estrutura de arquivos:

```
dashboard/
├── index.php (dashboard principal)
├── microsites.php (lista de microsites)
├── qrcodes.php (lista de QR codes)
├── analytics.php (estatísticas)
├── subscription.php (planos e pagamentos)
├── profile.php (perfil do usuário)
├── settings.php (configurações)
├── support.php (suporte)
└── includes/
    ├── navbar.php (barra superior)
    └── sidebar.php (menu lateral)
```

## 🔄 Migração de Dados

Se você já tem microsites em JSON, migre para o banco:

```php
<?php
// migration/migrate-microsites.php
require_once '../includes/database.php';

$db = Database::getInstance();
$files = glob('../microsites/*.json');

foreach ($files as $file) {
    $data = json_decode(file_get_contents($file), true);
    
    // Buscar ou criar usuário padrão
    $userId = 1; // Ajuste conforme necessário
    
    $db->query(
        "INSERT INTO microsites (user_id, slug, name, description, avatar, theme, widgets, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, 'active')",
        [
            $userId,
            basename($file, '.json'),
            $data['name'],
            $data['description'] ?? '',
            $data['avatar'] ?? '',
            json_encode($data['theme'] ?? []),
            json_encode($data['widgets'] ?? [])
        ]
    );
}

echo "Migração concluída!\n";
```

## 🔒 Segurança

### Implementado:

- ✅ Senhas com hash bcrypt (cost 12)
- ✅ Proteção contra SQL Injection (PDO prepared statements)
- ✅ Proteção contra CSRF (tokens de sessão)
- ✅ Cookies httpOnly
- ✅ Logs de auditoria
- ✅ Validação de entrada

### Recomendações adicionais:

1. **SSL/HTTPS** - Configure certificado SSL
2. **Rate Limiting** - Limite tentativas de login
3. **2FA** - Implementar autenticação de dois fatores
4. **Backup** - Automatize backups do banco

## 📧 Email (TODO)

Para enviar emails de verificação e recuperação de senha:

```php
// includes/email.php
function sendEmail($to, $subject, $body) {
    // Usar PHPMailer, SendGrid, AWS SES, etc
}
```

## 🧪 Teste Rápido

1. **Criar conta:**
```
http://localhost/QrCode/register.php
```

2. **Fazer login:**
```
http://localhost/QrCode/login.php
```

3. **Acessar dashboard:**
```
http://localhost/QrCode/dashboard/
```

4. **Escolher plano:**
```
http://localhost/QrCode/dashboard/subscription.php
```

## 🐛 Troubleshooting

### Erro: "Erro ao conectar ao banco de dados"
- Verifique credenciais em `includes/database.php`
- Confirme que o MySQL está rodando
- Verifique se o banco foi criado

### Erro: "Class 'PDO' not found"
- Habilite extensão PDO no php.ini
- Reinicie o servidor

### Erro 404 nas rotas
- Habilite mod_rewrite no Apache
- Verifique arquivo .htaccess

### Sessão não persiste
- Verifique permissões da pasta session
- Confirme que cookies estão habilitados

## 📞 Suporte

Para dúvidas ou problemas:
- GitHub Issues: https://github.com/hidalgojunior/qrcode/issues
- Email: suporte@devmenthors.com

---

**Última atualização:** 01/10/2025
