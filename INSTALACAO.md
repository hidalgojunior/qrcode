# 🚀 Instalação Rápida DevMenthors

## ⚡ Instalador Automático (Recomendado)

### Passo 1: Acesse o instalador
```
http://localhost/QrCode/install.php
```

### Passo 2: Configure o banco de dados
- **Servidor**: localhost (ou IP do seu MySQL)
- **Usuário**: root (ou seu usuário MySQL)
- **Senha**: (sua senha MySQL)
- **Banco**: devmenthors (será criado automaticamente)

### Passo 3: Clique em "Testar Conexão e Continuar"
O sistema irá:
- ✅ Verificar conexão com MySQL
- ✅ Criar o banco de dados automaticamente
- ✅ Instalar todas as tabelas
- ✅ Inserir dados iniciais (planos)
- ✅ Gerar arquivo `.env` com suas credenciais
- ✅ Marcar sistema como instalado

### Passo 4: Pronto! 🎉
- Acesse a home: `http://localhost/QrCode/home.php`
- Crie sua conta: `http://localhost/QrCode/register.php`

## 🔒 Segurança Pós-Instalação

**IMPORTANTE:** Após a instalação, delete o arquivo `install.php` por segurança!

```bash
# Windows (PowerShell)
Remove-Item install.php

# Linux/Mac
rm install.php
```

## 📋 Requisitos do Sistema

- ✅ PHP 7.4 ou superior
- ✅ MySQL 5.7 ou superior
- ✅ Apache/Nginx com mod_rewrite
- ✅ Extensões PHP: PDO, PDO_MySQL, GD, JSON, mbstring

## 🗄️ Estrutura do Banco de Dados

O instalador cria automaticamente:

### 📊 Tabelas Principais
- `users` - Usuários do sistema
- `plans` - Planos de assinatura
- `subscriptions` - Assinaturas ativas
- `payments` - Pagamentos via PIX
- `microsites` - Microsites criados
- `qrcodes` - QR Codes gerados
- `analytics` - Estatísticas de acesso
- `notifications` - Notificações
- `sessions` - Sessões de usuários
- `audit_logs` - Logs de auditoria
- `settings` - Configurações do sistema

### 💰 Planos Pré-configurados

#### Microsites
| Plano | Preço | Microsites | Marca d'água |
|-------|-------|------------|--------------|
| Básico | R$ 10 | 1 | Sim |
| Starter | R$ 20 | 1 | Não |
| Pro | R$ 70 | 10 | Não |
| Enterprise | Custom | Ilimitado | Não |

#### QR Codes
| Plano | Preço | QR Codes/mês | Marca d'água | Personalização |
|-------|-------|--------------|--------------|----------------|
| Grátis | R$ 0 | Ilimitado | Sim | Não |
| Starter | R$ 20 | 10 | Não | Sim |
| Pro | R$ 30 | 50 | Não | Completa |
| Enterprise | Custom | Ilimitado | Não | API |

## 📁 Arquivo .env Gerado

O instalador cria automaticamente o arquivo `.env`:

```env
DB_HOST=localhost
DB_NAME=devmenthors
DB_USER=root
DB_PASS=sua_senha
BASE_URL=http://localhost/QrCode
```

Você pode editar este arquivo manualmente se necessário.

## 🔧 Configuração Manual (Alternativa)

Se preferir instalar manualmente:

### 1. Criar banco de dados
```sql
CREATE DATABASE devmenthors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Importar schema
```bash
mysql -u root -p devmenthors < database/schema.sql
```

### 3. Configurar .env
Copie `.env.example` para `.env` e edite as credenciais:
```bash
cp .env.example .env
```

## ✅ Verificar Instalação

Após instalar, verifique:

1. **Banco de dados criado:**
   ```sql
   SHOW DATABASES LIKE 'devmenthors';
   ```

2. **Tabelas instaladas:**
   ```sql
   USE devmenthors;
   SHOW TABLES;
   ```
   Deve mostrar 11 tabelas.

3. **Planos inseridos:**
   ```sql
   SELECT * FROM plans;
   ```
   Deve mostrar 8 planos (4 de microsites + 4 de QR Code).

## 🐛 Problemas Comuns

### Erro: "Erro ao conectar"
- ✅ Verifique se o MySQL está rodando
- ✅ Confirme usuário e senha
- ✅ Verifique se o usuário tem permissão para criar bancos

### Erro: "Class 'PDO' not found"
- ✅ Habilite extensão PDO no `php.ini`:
  ```ini
  extension=pdo_mysql
  extension=mysqli
  ```
- ✅ Reinicie o servidor Apache/Nginx

### Erro: "Access denied"
- ✅ Grant privilégios ao usuário:
  ```sql
  GRANT ALL PRIVILEGES ON devmenthors.* TO 'root'@'localhost';
  FLUSH PRIVILEGES;
  ```

### Arquivo .installed já existe
- Se quiser reinstalar, delete o arquivo `.installed`:
  ```bash
  Remove-Item .installed
  ```

## 🎯 Próximos Passos

Após a instalação:

1. **Criar sua conta**
   ```
   http://localhost/QrCode/register.php
   ```

2. **Fazer login**
   ```
   http://localhost/QrCode/login.php
   ```

3. **Acessar dashboard**
   ```
   http://localhost/QrCode/dashboard/
   ```

4. **Criar seu primeiro microsite**
   ```
   http://localhost/QrCode/create-devmenthors.php
   ```

5. **Gerar QR Code**
   ```
   http://localhost/QrCode/index.php
   ```

## 📚 Documentação Adicional

- 📖 [INSTALL-DATABASE.md](INSTALL-DATABASE.md) - Instalação detalhada
- 🚀 [DEPLOY.md](DEPLOY.md) - Guia de deploy em produção
- 📋 [README.md](README.md) - Documentação completa do projeto

## 🆘 Suporte

Problemas na instalação?

- 📧 Email: suporte@devmenthors.com
- 🐛 GitHub Issues: https://github.com/hidalgojunior/qrcode/issues
- 💬 Consulte a [documentação completa](INSTALL-DATABASE.md)

---

**Desenvolvido com ❤️ por DevMenthors**

*Última atualização: 01/10/2025*
