# 🚀 Guia de Deploy - QRCode DevMenthors

Este documento descreve os passos para fazer o deploy da aplicação em diferentes ambientes.

## 📋 Pré-requisitos

- PHP >= 7.4
- Composer instalado
- Acesso SSH ao servidor (para deploy manual)
- Git instalado no servidor

## 🌐 Deploy em Hospedagem Compartilhada

### 1. Via FTP

```bash
# 1. Prepare os arquivos localmente
composer install --no-dev --optimize-autoloader

# 2. Faça upload dos arquivos via FTP
# - Faça upload de todos os arquivos EXCETO:
#   - /vendor/ (será criado no servidor)
#   - /.git/
#   - /microsites/*.json (dados de usuários)
#   - /node_modules/

# 3. No servidor, via SSH ou terminal da hospedagem:
cd /home/seu-usuario/public_html
composer install --no-dev --optimize-autoloader
chmod 755 microsites qrcodes
```

### 2. Via cPanel File Manager

1. Faça upload do arquivo .zip do projeto
2. Extraia no diretório público (public_html)
3. No terminal SSH:
```bash
cd public_html
composer install --no-dev
chmod 755 microsites qrcodes
```

### 3. Via Git (recomendado)

```bash
# No servidor
cd /home/seu-usuario/public_html
git clone https://github.com/hidalgojunior/qrcode.git .
composer deploy
```

## 🐳 Deploy com Docker (futuro)

```dockerfile
# Dockerfile de exemplo
FROM php:7.4-apache

# Instalar extensões
RUN docker-php-ext-install gd
RUN docker-php-ext-install pdo pdo_mysql

# Copiar arquivos
COPY . /var/www/html

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader

# Permissões
RUN chmod 755 /var/www/html/microsites
RUN chmod 755 /var/www/html/qrcodes

EXPOSE 80
```

## ☁️ Deploy em VPS/Cloud (AWS, DigitalOcean, etc)

### Nginx + PHP-FPM

```bash
# 1. Conectar ao servidor
ssh usuario@seu-servidor.com

# 2. Instalar dependências
sudo apt update
sudo apt install php7.4 php7.4-fpm php7.4-gd php7.4-json php7.4-mbstring composer nginx git

# 3. Clonar repositório
cd /var/www
sudo git clone https://github.com/hidalgojunior/qrcode.git
cd qrcode

# 4. Instalar dependências
sudo composer deploy

# 5. Configurar permissões
sudo chown -R www-data:www-data /var/www/qrcode
sudo chmod -R 755 /var/www/qrcode
sudo chmod -R 775 /var/www/qrcode/microsites
sudo chmod -R 775 /var/www/qrcode/qrcodes

# 6. Configurar Nginx
sudo nano /etc/nginx/sites-available/qrcode
```

**Configuração Nginx:**

```nginx
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    root /var/www/qrcode;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Cache de assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg)$ {
        expires 7d;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
# Ativar site e reiniciar Nginx
sudo ln -s /etc/nginx/sites-available/qrcode /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 🔄 Deploy Automático com Git Hooks

### post-receive Hook

```bash
# No servidor, criar hook
cd /var/www/qrcode.git
nano hooks/post-receive
```

**Conteúdo do hook:**

```bash
#!/bin/bash

TARGET="/var/www/qrcode"
GIT_DIR="/var/www/qrcode.git"
BRANCH="main"

while read oldrev newrev ref
do
    # Verifica se é a branch main
    if [[ $ref = refs/heads/"$BRANCH" ]];
    then
        echo "Ref $ref recebido. Fazendo deploy para ${BRANCH}..."
        git --work-tree=$TARGET --git-dir=$GIT_DIR checkout -f $BRANCH
        
        cd $TARGET
        
        echo "Instalando dependências..."
        composer install --no-dev --optimize-autoloader
        
        echo "Verificando permissões..."
        chmod 755 microsites qrcodes
        
        echo "Deploy concluído!"
    fi
done
```

```bash
# Tornar executável
chmod +x hooks/post-receive
```

### Adicionar remote no local

```bash
# No seu computador local
git remote add production usuario@servidor.com:/var/www/qrcode.git

# Para fazer deploy
git push production main
```

## 🔐 Configurações de Segurança

### 1. Arquivo .htaccess (Apache)

```apache
# Prevenir acesso a arquivos sensíveis
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Proteção de arquivos de configuração
<FilesMatch "(composer\.json|composer\.lock|\.env)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevenir listagem de diretórios
Options -Indexes

# Habilitar compressão
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Cache de browser
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 2. Variáveis de Ambiente

```bash
# Criar arquivo .env no servidor
cp .env.example .env
nano .env

# Editar com configurações de produção
APP_ENV=production
APP_DEBUG=false
BASE_URL=https://seudominio.com
```

### 3. Permissões Corretas

```bash
# Diretórios de escrita
chmod 775 microsites
chmod 775 qrcodes

# Arquivos sensíveis
chmod 600 .env
chmod 644 composer.json

# Executáveis PHP
chmod 644 *.php
```

## 📊 Monitoramento

### Logs de Erro

```bash
# Criar diretório de logs
mkdir /var/www/qrcode/logs
chmod 775 /var/www/qrcode/logs

# Adicionar ao php.ini ou .htaccess
php_value error_log /var/www/qrcode/logs/php-error.log
```

### Backup Automático

```bash
# Criar script de backup
nano /root/backup-qrcode.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/backups/qrcode"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup de arquivos
tar -czf $BACKUP_DIR/qrcode_files_$DATE.tar.gz /var/www/qrcode

# Backup dos microsites
tar -czf $BACKUP_DIR/qrcode_microsites_$DATE.tar.gz /var/www/qrcode/microsites

# Manter apenas últimos 7 dias
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup concluído: $DATE"
```

```bash
# Adicionar ao crontab
crontab -e

# Executar backup diário às 2h da manhã
0 2 * * * /root/backup-qrcode.sh
```

## ✅ Checklist de Deploy

- [ ] Todas as dependências instaladas
- [ ] Diretórios microsites e qrcodes criados
- [ ] Permissões configuradas corretamente
- [ ] Arquivo .env configurado
- [ ] Servidor web configurado (Apache/Nginx)
- [ ] PHP-FPM rodando (se aplicável)
- [ ] SSL/HTTPS configurado
- [ ] Firewall configurado
- [ ] Backup automático configurado
- [ ] Monitoramento de logs ativo
- [ ] Teste de criação de QR Code
- [ ] Teste de criação de DevMenthors
- [ ] Teste de upload de imagens

## 🆘 Troubleshooting

### Erro: "Permission denied"
```bash
chmod -R 775 microsites qrcodes
chown -R www-data:www-data microsites qrcodes
```

### Erro: "composer command not found"
```bash
# Instalar composer globalmente
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Erro: "Class not found"
```bash
composer dump-autoload
```

### Erro 500
```bash
# Verificar logs
tail -f /var/log/nginx/error.log
tail -f /var/www/qrcode/logs/php-error.log
```

## 📞 Suporte

Para mais informações ou problemas, abra uma issue no GitHub:
https://github.com/hidalgojunior/qrcode/issues

---

**Última atualização:** 01/10/2025
