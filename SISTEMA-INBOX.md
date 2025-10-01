# 📥 Sistema de Gerenciamento de Inbox

Sistema completo para receber, categorizar e responder emails via IMAP/SMTP.

## 🎯 Funcionalidades

### ✅ Implementadas:
- **Recebimento automático de emails** via IMAP (Hostinger)
- **Categorização inteligente** (Cliente, Suporte, Geral, Spam)
- **Interface web completa** para gerenciar emails
- **Resposta direta** aos emails recebidos
- **Armazenamento em banco de dados** com histórico
- **Processamento de anexos**
- **Estatísticas e métricas**
- **Busca e filtros** avançados
- **Marcação de leitura** automática
- **Cron job** para busca automática

---

## 📦 Arquivos Criados

### **Backend:**
1. `includes/inbox.php` - Classe InboxManager (540 linhas)
2. `api/inbox.php` - API REST para gerenciar emails
3. `process-inbox.php` - Script cron para buscar emails

### **Frontend:**
4. `inbox.php` - Interface web completa
5. `test-inbox.php` - Página de testes

### **Database:**
6. `database/inbox-schema.sql` - Schema das tabelas
7. `database/create-inbox-tables.php` - Script para criar tabelas

### **Tabelas MySQL:**
- `inbox_emails` - Emails recebidos
- `inbox_replies` - Histórico de respostas

---

## ⚙️ Configuração

### **1. Credenciais (.env)**

```env
# SMTP - Envio de Emails
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=465
SMTP_USER=contato@devmenthors.shop
SMTP_PASS=@Jr34139251
SMTP_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=contato@devmenthors.shop
MAIL_FROM_NAME=DevMenthors

# IMAP - Recebimento de Emails
IMAP_HOST=imap.hostinger.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
INBOX_EMAIL=contato@devmenthors.shop
INBOX_PASSWORD=@Jr34139251
```

### **2. Hostinger - Configurações**

**Servidor IMAP (Receber):**
- Host: `imap.hostinger.com`
- Port: `993`
- Encryption: `SSL`

**Servidor SMTP (Enviar):**
- Host: `smtp.hostinger.com`
- Port: `465`
- Encryption: `SSL`

**Credenciais:**
- Email: `contato@devmenthors.shop`
- Senha: `@Jr34139251`

---

## 🗄️ Estrutura do Banco de Dados

### **Tabela: inbox_emails**

```sql
CREATE TABLE inbox_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id VARCHAR(255) UNIQUE,         -- ID único do email
    from_email VARCHAR(255) NOT NULL,       -- Email do remetente
    from_name VARCHAR(255),                 -- Nome do remetente
    subject TEXT,                           -- Assunto
    body LONGTEXT,                          -- Corpo (HTML)
    category ENUM('cliente', 'spam', 'suporte', 'geral') DEFAULT 'geral',
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    attachments JSON,                       -- Array de anexos
    imap_id VARCHAR(50),                    -- ID IMAP
    received_at DATETIME,                   -- Data de recebimento
    replied_at DATETIME,                    -- Data da resposta
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_from_email (from_email),
    INDEX idx_received_at (received_at)
);
```

### **Tabela: inbox_replies**

```sql
CREATE TABLE inbox_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email_id INT NOT NULL,                  -- FK para inbox_emails
    reply_body LONGTEXT,                    -- Corpo da resposta
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (email_id) REFERENCES inbox_emails(id) ON DELETE CASCADE
);
```

---

## 🚀 Como Usar

### **1. Testar Conexão**

```bash
# Abrir página de testes
http://localhost/QrCode/test-inbox.php
```

**Ou via API:**
```php
require_once 'includes/inbox.php';

$inbox = new InboxManager();
$result = $inbox->testConnection();

if ($result['success']) {
    echo "Conectado! Mensagens: " . $result['info']['messages'];
} else {
    echo "Erro: " . $result['error'];
}
```

### **2. Buscar Emails Manualmente**

```bash
php process-inbox.php
```

**Ou via API:**
```bash
curl "http://localhost/QrCode/api/inbox.php?action=fetch"
```

### **3. Acessar Interface Web**

```bash
http://localhost/QrCode/inbox.php
```

**Recursos da Interface:**
- ✅ Lista de emails com paginação
- ✅ Filtros por categoria, status e busca
- ✅ Visualização completa do email
- ✅ Responder diretamente
- ✅ Deletar emails
- ✅ Estatísticas em tempo real
- ✅ Download de anexos
- ✅ Auto-refresh a cada 5 minutos

---

## 🤖 Processamento Automático (Cron)

### **Script:** `process-inbox.php`

**Funções:**
- Busca novos emails do servidor IMAP
- Processa até 50 emails por execução
- Categoriza automaticamente
- Salva no banco de dados
- Gera logs diários

### **Configurar Cron:**

**Linux/Mac:**
```bash
crontab -e

# Buscar emails a cada 5 minutos
*/5 * * * * php /caminho/para/process-inbox.php
```

**Windows (Task Scheduler):**
```powershell
schtasks /create /sc minute /mo 5 /tn "InboxCheck" /tr "php c:\laragon\www\QrCode\process-inbox.php"
```

**Via Web (com secret):**
```bash
curl "http://localhost/QrCode/process-inbox.php?secret=cron_secret_2025"
```

---

## 📊 Categorização Automática

### **Algoritmo:**

1. **SPAM** - Se contém palavras spam:
   - "ganhe dinheiro", "clique aqui", "promoção imperdível"
   - "você ganhou", "prêmio", "loteria", "urgente"

2. **CLIENTE** - Se:
   - Email está cadastrado no sistema (tabela `users`)
   - OU contém palavras: "assinatura", "plano", "pagamento", "fatura"
   - OU contém: "qr code", "microsite", "suporte técnico"

3. **SUPORTE** - Se contém:
   - "suporte", "ajuda", "problema", "erro", "não funciona"

4. **GERAL** - Padrão para outros emails

---

## 🔧 API Endpoints

### **GET - Listar Emails**
```bash
GET /api/inbox.php?action=list&category=cliente&status=unread&page=1&limit=20
```

**Resposta:**
```json
{
  "success": true,
  "emails": [...],
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 45,
    "pages": 3
  }
}
```

### **GET - Visualizar Email**
```bash
GET /api/inbox.php?action=view&id=123
```

**Resposta:**
```json
{
  "success": true,
  "email": {
    "id": 123,
    "from_email": "cliente@example.com",
    "subject": "Dúvida sobre assinatura",
    "body": "<html>...",
    "category": "cliente",
    "status": "read",
    "attachments": [...]
  }
}
```

### **GET - Estatísticas**
```bash
GET /api/inbox.php?action=stats
```

**Resposta:**
```json
{
  "success": true,
  "stats": {
    "total": 156,
    "unread": 23,
    "by_category": {
      "cliente": 45,
      "suporte": 12,
      "geral": 89,
      "spam": 10
    },
    "by_status": {
      "unread": 23,
      "read": 98,
      "replied": 35
    }
  }
}
```

### **GET - Buscar Novos**
```bash
GET /api/inbox.php?action=fetch
```

**Resposta:**
```json
{
  "success": true,
  "result": {
    "processed": 5,
    "total": 5,
    "errors": []
  },
  "stats": {...}
}
```

### **POST - Responder Email**
```bash
POST /api/inbox.php
Content-Type: application/json

{
  "action": "reply",
  "email_id": 123,
  "reply_body": "Olá! Obrigado pelo contato..."
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Resposta enviada com sucesso"
}
```

### **POST - Deletar Email**
```bash
POST /api/inbox.php
Content-Type: application/json

{
  "action": "delete",
  "email_id": 123
}
```

---

## 💻 Uso Programático

### **Buscar Novos Emails:**

```php
require_once 'includes/inbox.php';

$inbox = new InboxManager();
$result = $inbox->fetchNewEmails(50);

echo "Processados: {$result['processed']} de {$result['total']}";
```

### **Listar Emails:**

```php
$filters = [
    'category' => 'cliente',
    'status' => 'unread',
    'search' => 'assinatura',
    'page' => 1,
    'limit' => 20
];

$result = $inbox->listEmails($filters);

foreach ($result['emails'] as $email) {
    echo "{$email['subject']} - {$email['from_email']}\n";
}
```

### **Responder Email:**

```php
$emailId = 123;
$replyBody = "Olá! Obrigado pelo contato. Vou verificar sua solicitação...";

$result = $inbox->replyEmail($emailId, $replyBody);

if ($result['success']) {
    echo "Resposta enviada!";
}
```

### **Estatísticas:**

```php
$stats = $inbox->getStats();

echo "Total: {$stats['total']}\n";
echo "Não lidos: {$stats['unread']}\n";
echo "Clientes: {$stats['by_category']['cliente']}\n";
```

---

## 📝 Logs

### **Arquivo:** `logs/inbox-YYYY-MM-DD.log`

**Exemplo:**
```
[2025-10-01 14:30:15] === Iniciando busca de emails ===
[2025-10-01 14:30:16] Testando conexão IMAP...
[2025-10-01 14:30:17] Conexão OK - 156 mensagens no servidor
[2025-10-01 14:30:17] Não lidas: 23
[2025-10-01 14:30:18] Buscando emails novos...
[2025-10-01 14:30:25] Processados: 5 de 5 emails
[2025-10-01 14:30:25] Estatísticas:
[2025-10-01 14:30:25] - Total de emails: 156
[2025-10-01 14:30:25] - Não lidos: 23
[2025-10-01 14:30:25] Por categoria:
[2025-10-01 14:30:25]   - cliente: 45
[2025-10-01 14:30:25]   - suporte: 12
[2025-10-01 14:30:25] === Busca concluída com sucesso ===
```

---

## 🛠️ Troubleshooting

### **Erro: "IMAP connect() failed"**

**Causas:**
- Credenciais incorretas
- Porta bloqueada (993)
- Extensão PHP IMAP não instalada

**Solução:**
```bash
# Verificar extensão IMAP
php -m | grep imap

# Se não estiver instalada:
# Windows (Laragon): Ativar em php.ini
extension=imap

# Linux:
sudo apt install php-imap
```

### **Erro: "Connection timed out"**

**Causas:**
- Firewall bloqueando porta 993
- Servidor IMAP indisponível

**Solução:**
```bash
# Testar conexão manual
telnet imap.hostinger.com 993
```

### **Emails não sendo categorizados como cliente**

**Solução:**
1. Verificar se o email está na tabela `users`
2. Adicionar palavras-chave em `$clientKeywords`
3. Revisar algoritmo em `categorizeEmail()`

### **Anexos não sendo salvos**

**Solução:**
```bash
# Criar diretório e dar permissões
mkdir attachments
chmod 755 attachments
```

---

## 📈 Métricas e Monitoramento

### **SQL - Emails por Categoria:**
```sql
SELECT category, COUNT(*) as total 
FROM inbox_emails 
GROUP BY category;
```

### **SQL - Emails Não Lidos:**
```sql
SELECT COUNT(*) FROM inbox_emails WHERE status = 'unread';
```

### **SQL - Taxa de Resposta:**
```sql
SELECT 
    COUNT(CASE WHEN status = 'replied' THEN 1 END) * 100.0 / COUNT(*) as taxa_resposta
FROM inbox_emails
WHERE category = 'cliente';
```

### **SQL - Tempo Médio de Resposta:**
```sql
SELECT 
    AVG(TIMESTAMPDIFF(MINUTE, received_at, replied_at)) as minutos_media
FROM inbox_emails
WHERE status = 'replied';
```

---

## 🎯 Próximos Passos

### **1. Notificações em Tempo Real:**
```php
// WebSocket para notificar novos emails
// Push notification no navegador
```

### **2. Respostas Automáticas:**
```php
// Bot para respostas padrão
// Templates de resposta rápida
```

### **3. Integração com Tickets:**
```php
// Converter email em ticket de suporte
// Sistema de atribuição automática
```

### **4. Machine Learning:**
```php
// Melhorar categorização com ML
// Detectar sentimento (positivo/negativo)
// Priorização inteligente
```

### **5. Multi-conta:**
```php
// Gerenciar múltiplas contas de email
// Suporte a domínios diferentes
```

---

## ✅ Checklist de Implementação

- [x] Instalação PHP-IMAP
- [x] Classe InboxManager
- [x] Conexão IMAP/SMTP Hostinger
- [x] Busca de emails
- [x] Categorização automática
- [x] Interface web completa
- [x] API REST
- [x] Responder emails
- [x] Anexos
- [x] Cron job
- [x] Logs
- [x] Estatísticas
- [x] Filtros e busca
- [x] Paginação
- [x] Tabelas MySQL
- [x] Documentação
- [ ] Notificações push
- [ ] Respostas automáticas
- [ ] Integração com tickets
- [ ] Machine Learning

---

## 📞 Suporte

**Email configurado:** contato@devmenthors.shop  
**Servidor:** Hostinger  
**Protocolo:** IMAP/SMTP via SSL

**Testes:**
1. http://localhost/QrCode/test-inbox.php
2. http://localhost/QrCode/inbox.php

**Logs:** `logs/inbox-YYYY-MM-DD.log`

---

**🎉 Sistema de Inbox 100% Funcional!**

*Desenvolvido para DevMenthors*  
*Data: 01/10/2025*
