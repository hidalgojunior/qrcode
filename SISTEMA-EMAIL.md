# 📧 SISTEMA DE EMAIL IMPLEMENTADO

## ✅ Implementação Completa

Sistema completo de envio de emails com PHPMailer, templates HTML responsivos e fila de processamento.

---

## 📦 Arquivos Criados

### **Sistema de Email:**
1. ✅ `includes/email.php` - Classe Email com PHPMailer (420 linhas)
2. ✅ `process-email-queue.php` - Script cron para processar fila
3. ✅ `api/test-email.php` - API de teste
4. ✅ `api/test-email-interface.php` - Interface HTML de teste

### **Templates HTML:**
5. ✅ `templates/email/welcome.html` - Email de boas-vindas (120 linhas)
6. ✅ `templates/email/verify-email.html` - Verificação de email
7. ✅ `templates/email/reset-password.html` - Recuperação de senha
8. ✅ `templates/email/payment-confirmed.html` - Pagamento confirmado (150 linhas)
9. ✅ `templates/email/subscription-expiring.html` - Assinatura expirando

### **Banco de Dados:**
10. ✅ `email_queue` - Tabela de fila de emails
11. ✅ `email_logs` - Tabela de logs de envio

### **Configuração:**
12. ✅ `.env` - Adicionadas configurações SMTP
13. ✅ `.env.example` - Atualizado com exemplo

### **Outros:**
14. ✅ `generator.php` - Gerador de QR Code (antigo index.php)
15. ✅ `index.php` - Redireciona para home.php

---

## 🎯 Funcionalidades

### **Classe Email (includes/email.php)**

#### **Métodos Principais:**
- ✅ `sendWelcome()` - Email de boas-vindas
- ✅ `sendVerification()` - Verificação de email
- ✅ `sendPasswordReset()` - Recuperação de senha
- ✅ `sendPaymentConfirmed()` - Pagamento confirmado
- ✅ `sendSubscriptionExpiring()` - Assinatura expirando
- ✅ `send()` - Envio genérico
- ✅ `processQueue()` - Processar fila
- ✅ `testConnection()` - Testar SMTP

#### **Recursos:**
- ✅ Configuração via .env
- ✅ Suporte a anexos
- ✅ Templates HTML responsivos
- ✅ Fila de emails (opcional)
- ✅ Retry automático (exponential backoff)
- ✅ Logs de envio
- ✅ Processamento idempotente

---

## ⚙️ Configuração

### **1. Instalar PHPMailer**
```bash
composer require phpmailer/phpmailer
```

### **2. Configurar SMTP no .env**

#### **Gmail:**
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=seu@gmail.com
SMTP_PASS=sua_senha_de_app
SMTP_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@devmenthors.com
MAIL_FROM_NAME=DevMenthors
```

**Como gerar senha de app no Gmail:**
1. Acesse: https://myaccount.google.com/security
2. Ative "Verificação em duas etapas"
3. Acesse "Senhas de app"
4. Gere uma senha para "Email"
5. Use essa senha no `SMTP_PASS`

#### **Outros Provedores:**

**SendGrid:**
```env
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_USER=apikey
SMTP_PASS=SG.xxxxxxxxxxxx
```

**Mailgun:**
```env
SMTP_HOST=smtp.mailgun.org
SMTP_PORT=587
SMTP_USER=postmaster@seudominio.com
SMTP_PASS=sua_senha_mailgun
```

**Amazon SES:**
```env
SMTP_HOST=email-smtp.us-east-1.amazonaws.com
SMTP_PORT=587
SMTP_USER=suas_credenciais_iam
SMTP_PASS=sua_senha_ses
```

### **3. Criar Tabelas no Banco**
```sql
-- Fila de emails
CREATE TABLE email_queue (
  id int(11) NOT NULL AUTO_INCREMENT,
  to_email varchar(255) NOT NULL,
  to_name varchar(255) DEFAULT NULL,
  subject varchar(255) NOT NULL,
  body text NOT NULL,
  attachments text DEFAULT NULL,
  status enum('pending','processing','sent','failed') DEFAULT 'pending',
  attempts int(11) DEFAULT 0,
  error text DEFAULT NULL,
  scheduled_at datetime DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sent_at datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Logs de email
CREATE TABLE email_logs (
  id int(11) NOT NULL AUTO_INCREMENT,
  to_email varchar(255) NOT NULL,
  subject varchar(255) NOT NULL,
  status enum('sent','failed') NOT NULL,
  error text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
```

---

## 🧪 Como Testar

### **1. Interface de Teste**
```
http://localhost/QrCode/api/test-email-interface.php
```

**Funcionalidades:**
- Enviar emails de teste
- Testar conexão SMTP
- Visualizar fila
- Processar fila manualmente

### **2. Teste via Código**
```php
require_once 'includes/email.php';

$email = new Email();

// Enviar boas-vindas
$email->sendWelcome('usuario@example.com', 'João Silva', 'Starter');

// Enviar verificação
$email->sendVerification('usuario@example.com', 'João Silva', 'TOKEN_123');

// Enviar recuperação de senha
$email->sendPasswordReset('usuario@example.com', 'João Silva', 'RESET_456');

// Enviar pagamento confirmado
$email->sendPaymentConfirmed('usuario@example.com', 'João Silva', 'Pro', 70.00, 'TXN_789');

// Enviar assinatura expirando
$email->sendSubscriptionExpiring('usuario@example.com', 'João Silva', 'Pro', 7, '2025-10-08');
```

### **3. Testar Conexão SMTP**
```php
require_once 'includes/email.php';

$email = new Email();
$connected = $email->testConnection();

if ($connected) {
    echo "✅ Conexão SMTP OK!";
} else {
    echo "❌ Falha na conexão SMTP!";
}
```

---

## ⚙️ Processamento de Fila

### **Modo 1: Cron Job (Recomendado)**

#### **Linux/Mac:**
```bash
# Editar crontab
crontab -e

# Adicionar linha (executa a cada 5 minutos)
*/5 * * * * php /caminho/completo/para/process-email-queue.php
```

#### **Windows:**
```powershell
# Criar Task Scheduler
schtasks /create /sc minute /mo 5 /tn "ProcessEmailQueue" /tr "php c:\laragon\www\QrCode\process-email-queue.php"
```

### **Modo 2: Via Web (Desenvolvimento)**
```
http://localhost/QrCode/process-email-queue.php?secret=cron_secret_2025
```

**⚠️ Importante:** Em produção, remova o acesso via web ou proteja com secret forte!

### **Modo 3: Manual via CLI**
```bash
php process-email-queue.php
```

---

## 📊 Fluxo de Processamento

### **Com Fila (Padrão):**
```
1. Usuário se registra
2. Email adicionado à tabela email_queue (status: pending)
3. Retorno imediato ao usuário
4. Cron executa process-email-queue.php a cada 5 min
5. Script processa até 50 emails
6. Email enviado via SMTP
7. Status atualizado para 'sent'
8. Log registrado em email_logs
```

### **Sem Fila (Imediato):**
```
1. Usuário se registra
2. Email enviado imediatamente
3. Aguarda resposta do SMTP (pode ser lento)
4. Log registrado em email_logs
5. Retorno ao usuário
```

### **Retry Policy:**
```
Tentativa 1: Imediata
Tentativa 2: Após 2 minutos (se falhar)
Tentativa 3: Após 4 minutos (se falhar)
Status final: 'failed' (após 3 tentativas)
```

---

## 🎨 Templates de Email

### **Variáveis Disponíveis:**

#### **Globais (Todos os templates):**
- `{{year}}` - Ano atual
- `{{base_url}}` - URL base do site
- `{{company_name}}` - Nome da empresa

#### **welcome.html:**
- `{{name}}` - Nome do usuário
- `{{plan_name}}` - Nome do plano
- `{{dashboard_url}}` - URL do dashboard
- `{{login_url}}` - URL de login

#### **verify-email.html:**
- `{{name}}` - Nome do usuário
- `{{verify_url}}` - URL de verificação
- `{{token}}` - Token de verificação

#### **reset-password.html:**
- `{{name}}` - Nome do usuário
- `{{reset_url}}` - URL de reset
- `{{token}}` - Token de reset

#### **payment-confirmed.html:**
- `{{name}}` - Nome do usuário
- `{{plan_name}}` - Nome do plano
- `{{amount}}` - Valor pago (formatado)
- `{{transaction_id}}` - ID da transação
- `{{dashboard_url}}` - URL do dashboard
- `{{invoice_url}}` - URL da fatura

#### **subscription-expiring.html:**
- `{{name}}` - Nome do usuário
- `{{plan_name}}` - Nome do plano
- `{{days_left}}` - Dias restantes
- `{{end_date}}` - Data de expiração
- `{{renew_url}}` - URL de renovação
- `{{support_url}}` - URL de suporte

---

## 🔧 Integração com Sistema

### **Ao Registrar Usuário:**
```php
// api/register.php
$email = new Email();
$email->sendWelcome($userEmail, $userName, $planName);
```

### **Ao Confirmar Pagamento:**
```php
// api/webhook-payment.php
$email = new Email();
$email->sendPaymentConfirmed($userEmail, $userName, $planName, $amount, $transactionId);
```

### **Ao Detectar Expiração Próxima:**
```php
// cron-check-expiring.php
$sql = "SELECT u.email, u.name, p.name as plan_name, s.end_date
        FROM subscriptions s
        JOIN users u ON s.user_id = u.id
        JOIN plans p ON s.plan_id = p.id
        WHERE s.status = 'active'
        AND DATE_DIFF(s.end_date, NOW()) <= 7
        AND DATE_DIFF(s.end_date, NOW()) > 0";

$expiring = $db->fetchAll($sql);

foreach ($expiring as $sub) {
    $daysLeft = ceil((strtotime($sub['end_date']) - time()) / 86400);
    $email->sendSubscriptionExpiring(
        $sub['email'],
        $sub['name'],
        $sub['plan_name'],
        $daysLeft,
        $sub['end_date']
    );
}
```

---

## 📈 Monitoramento

### **Verificar Fila:**
```sql
-- Emails pendentes
SELECT COUNT(*) FROM email_queue WHERE status = 'pending';

-- Emails falhados
SELECT * FROM email_queue WHERE status = 'failed';

-- Últimos emails enviados
SELECT * FROM email_logs ORDER BY created_at DESC LIMIT 10;
```

### **Estatísticas:**
```sql
-- Taxa de sucesso
SELECT 
  status,
  COUNT(*) as total,
  ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM email_logs), 2) as percentage
FROM email_logs
GROUP BY status;

-- Emails por dia
SELECT 
  DATE(created_at) as date,
  COUNT(*) as total,
  SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
  SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
FROM email_logs
GROUP BY DATE(created_at)
ORDER BY date DESC
LIMIT 30;
```

---

## 🐛 Troubleshooting

### **Erro: "SMTP connect() failed"**
- Verificar credenciais no .env
- Verificar se 2FA está ativo (Gmail)
- Gerar senha de app (Gmail)
- Verificar firewall/porta bloqueada

### **Emails não estão sendo enviados**
- Verificar se cron está rodando
- Verificar logs: `logs/email-queue-YYYY-MM-DD.log`
- Processar manualmente: `php process-email-queue.php`
- Verificar tabela `email_queue`

### **Emails vão para SPAM**
- Configurar SPF record no DNS
- Configurar DKIM no DNS
- Usar domínio próprio no FROM
- Evitar palavras spam no assunto

### **Fila crescendo muito**
- Aumentar frequência do cron (1 minuto)
- Aumentar limite de processamento (50 → 100)
- Verificar erros no SMTP
- Considerar usar serviço dedicado (SendGrid, Mailgun)

---

## ✅ Status da Implementação

- [x] Classe Email com PHPMailer
- [x] 5 templates HTML responsivos
- [x] Sistema de fila com retry
- [x] Logs de envio
- [x] Script cron
- [x] Interface de teste
- [x] Configuração via .env
- [x] Documentação completa
- [ ] Integração com registro (próximo)
- [ ] Integração com pagamento (próximo)
- [ ] Cron de assinaturas expirando (próximo)

---

## 🚀 Próximos Passos

1. **Integrar com Registro de Usuários:**
   - Enviar welcome email
   - Enviar verification email

2. **Integrar com Webhook de Pagamento:**
   - Enviar payment-confirmed email

3. **Criar Cron de Expiração:**
   - Verificar assinaturas expirando
   - Enviar alertas 7, 3, 1 dia antes

4. **Dashboard Admin:**
   - Ver logs de emails
   - Reenviar emails falhados
   - Estatísticas de envio

---

**Desenvolvido com ❤️ para DevMenthors**
*Implementado em: 01/10/2025*
*Status: ✅ 100% Funcional*
