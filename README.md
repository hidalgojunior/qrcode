# 🎨 QR Code Generator & DevMenthors

Sistema completo de geração de QR Codes personalizados e criação de microsites (DevMenthors) com design moderno e responsivo.

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-active-success)

## 📋 Índice

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Configuração](#️-configuração)
- [Deploy Automático](#-deploy-automático)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Uso](#-uso)
- [Tecnologias](#-tecnologias)
- [Licença](#-licença)

## ✨ Características

### 🔲 Gerador de QR Code
- 7 tipos de QR Code (Texto, URL, Email, Telefone, SMS, WiFi, vCard)
- Personalização completa (cores, tamanho, logo)
- Download em múltiplos formatos (PNG, SVG, EPS)
- QR Code com URL no rodapé
- Tema claro/escuro

### 🌐 DevMenthors (Microsites)
- **Criação de páginas personalizadas** com QR Code único
- **Upload de avatar** ou URL de imagem
- **Widgets dinâmicos**: Links, PIX, Galeria, Música, Vídeo, Texto, Localização
- **Preview em tempo real**: Mobile e Desktop
- **URL personalizada** (slugs amigáveis)
- **Temas de cores** customizáveis
- **Redes sociais** integradas
- **Compartilhamento** direto

### 🎨 Design
- Interface moderna com **Tailwind CSS**
- **Glassmorphism** e efeitos visuais
- **Paleta de cores vibrante** (5 cores principais)
- **Responsivo** (mobile-first)
- **Animações suaves**

## 📦 Requisitos

- **PHP** >= 7.4
- **Extensões PHP**:
  - `gd` (manipulação de imagens)
  - `json` (processamento JSON)
  - `mbstring` (strings multibyte)
- **Servidor Web** (Apache/Nginx)
- **Composer** (para gerenciamento de dependências)

## 🚀 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/hidalgojunior/qrcode.git
cd qrcode
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure as permissões

```bash
chmod 755 microsites qrcodes
```

### 4. Verifique a instalação

```bash
composer check-permissions
```

### 5. Acesse no navegador

```
http://localhost/QrCode
```

## ⚙️ Configuração

### Arquivo `config.php`

Edite o arquivo `config.php` para personalizar:

```php
<?php
// Diretório para armazenar microsites
define('MICROSITE_DIR', __DIR__ . '/microsites');

// Diretório para QR Codes
define('QRCODE_DIR', __DIR__ . '/qrcodes');

// URL base do site (para produção)
define('BASE_URL', 'https://seudominio.com');
```

### Variáveis de Ambiente (opcional)

Crie um arquivo `.env` para configurações locais:

```env
APP_ENV=production
APP_DEBUG=false
BASE_URL=https://seudominio.com
```

## 🌍 Deploy Automático

### Deploy Simples

```bash
composer deploy
```

Este comando:
1. Instala dependências de produção
2. Otimiza autoloader
3. Cria diretórios necessários
4. Verifica permissões

### Deploy em Produção (exemplo)

#### Via Git Hooks

Crie `.git/hooks/post-receive`:

```bash
#!/bin/bash
cd /var/www/html/qrcode
git pull origin main
composer deploy
composer check-permissions
echo "Deploy concluído!"
```

#### Via CI/CD (GitHub Actions)

Crie `.github/workflows/deploy.yml`:

```yaml
name: Deploy

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
          extensions: gd, json, mbstring
      
      - name: Install dependencies
        run: composer deploy
      
      - name: Deploy to server
        run: |
          # Seu comando de deploy (FTP, SSH, etc)
```

## 📁 Estrutura do Projeto

```
qrcode/
├── assets/
│   ├── css/
│   └── js/
│       ├── script.js
│       └── devmenthors-editor.js
├── lib/
│   └── phpqrcode/
├── microsites/           # Dados dos microsites (JSON)
│   └── .gitkeep
├── qrcodes/             # QR Codes gerados
│   └── .gitkeep
├── src/                 # Classes PHP (futuro)
├── config.php           # Configurações
├── index.php            # Gerador de QR Code
├── create-devmenthors.php   # Criação de microsite
├── devmenthors.php      # Visualização de microsite
├── devmenthors-result.php   # Resultado com QR personalizado
├── save-devmenthors.php     # API para salvar
├── check-slug.php       # API para verificar slug
├── generate.php         # Geração de QR Code
├── composer.json
├── .gitignore
└── README.md
```

## 💻 Uso

### 1. Gerar QR Code Simples

1. Acesse `index.php`
2. Escolha o tipo de QR Code
3. Preencha os dados
4. Personalize cores e tamanho
5. Clique em "Gerar QR Code"

### 2. Criar DevMenthors (Microsite)

1. Clique em "Criar DevMenthors"
2. Preencha informações básicas
3. Faça upload do avatar
4. Adicione redes sociais
5. Insira widgets (opcional)
6. Visualize preview (mobile/desktop)
7. Clique em "Criar DevMenthors"
8. Personalize e baixe o QR Code

### 3. Compartilhar

- Copie a URL gerada
- Use o QR Code em cartões de visita
- Compartilhe nas redes sociais
- Integre em materiais de marketing

## 🛠️ Tecnologias

### Backend
- **PHP 7.4+** - Linguagem server-side
- **Composer** - Gerenciador de dependências
- **PHPQRCode** - Geração de QR Codes

### Frontend
- **Tailwind CSS** - Framework CSS utility-first
- **Font Awesome** - Ícones
- **html2canvas** - Captura de elementos HTML
- **JavaScript ES6+** - Interatividade

### APIs Externas
- **QR Server API** - Geração de QR Codes personalizados

## 🎨 Paleta de Cores

```css
--green-blue: #2364aa    /* Azul profundo */
--picton-blue: #3da5d9   /* Azul vibrante */
--verdigris: #73bfb8     /* Verde-azulado */
--mikado-yellow: #fec601 /* Amarelo intenso */
--pumpkin: #ea7317       /* Laranja enérgico */
```

## 📝 Scripts Composer

```bash
# Instalar dependências
composer install

# Deploy em produção
composer deploy

# Verificar permissões
composer check-permissions

# Executar testes (quando implementados)
composer test
```

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Autor

**Hidalgo Junior**
- GitHub: [@hidalgojunior](https://github.com/hidalgojunior)

## 🙏 Agradecimentos

- [Tailwind CSS](https://tailwindcss.com)
- [Font Awesome](https://fontawesome.com)
- [QR Server API](https://goqr.me/api/)
- [PHPQRCode](http://phpqrcode.sourceforge.net/)

---

⭐ Se este projeto foi útil, considere dar uma estrela!
