# 🔗 Gerador de QR Code

Um sistema completo em PHP para gerar QR codes de diferentes tipos de forma rápida e intuitiva.

## 📋 Características

- **Múltiplos tipos de QR Code:**
  - 📝 Texto simples
  - 🔗 URLs/Links
  - 📧 Email (com assunto e mensagem)
  - 📞 Telefone
  - 💬 SMS
  - 📶 WiFi (conexão automática)
  - 👤 Cartão de visita (vCard)

- **Configurações flexíveis:**
  - Diferentes tamanhos (200x200 a 500x500)
  - Níveis de correção de erro
  - Download em PNG e JPG
  - Função de impressão
  - Compartilhamento via WhatsApp e email

## 🚀 Instalação

### Requisitos
- PHP 7.4 ou superior
- Extensão GD ativada
- Servidor web (Apache/Nginx)
- Acesso à internet (para geração de QR codes)

### Passos de instalação

1. **Clone ou baixe os arquivos**
   ```bash
   git clone [URL_DO_REPOSITORIO]
   # ou baixe e extraia o ZIP
   ```

2. **Configure as permissões**
   ```bash
   chmod 755 qrcodes/
   chmod 755 logs/
   chmod 755 tmp/
   ```

3. **Configure o servidor web**
   - Aponte o document root para a pasta do projeto
   - Certifique-se de que o mod_rewrite está ativado (Apache)

4. **Teste a instalação**
   - Acesse `http://localhost/QrCode/` (ou seu domínio)
   - Crie um QR code de teste

## 📁 Estrutura do Projeto

```
QrCode/
├── assets/
│   ├── css/
│   │   └── style.css          # Estilos do sistema
│   └── js/
│       └── script.js          # JavaScript para formulários
├── lib/
│   └── QRCodeGenerator.php    # Classe principal para geração
├── qrcodes/                   # Diretório dos QR codes gerados
├── logs/                      # Logs do sistema
├── tmp/                       # Arquivos temporários
├── index.php                  # Página principal
├── generate.php               # Processamento do formulário
├── result.php                 # Exibição do resultado
├── download.php               # Download dos arquivos
├── help.php                   # Página de ajuda
├── config.php                 # Configurações do sistema
├── .htaccess                  # Configurações do Apache
└── README.md                  # Este arquivo
```

## ⚙️ Configuração

### Configurações principais (config.php)

```php
// Tamanho padrão dos QR codes
define('DEFAULT_SIZE', 300);

// Limpeza automática de arquivos antigos
define('AUTO_CLEANUP_ENABLED', true);
define('MAX_FILE_AGE_HOURS', 24);

// Limite de requisições por hora
define('RATE_LIMIT_ENABLED', true);
define('MAX_REQUESTS_PER_HOUR', 100);
```

### Configurações do servidor

Para **Apache**, o arquivo `.htaccess` já está configurado.

Para **Nginx**, adicione ao seu arquivo de configuração:
```nginx
location ~* \.(log|txt)$ {
    deny all;
}

location /logs/ {
    deny all;
}

location /tmp/ {
    deny all;
}
```

## 🎯 Como Usar

1. **Acesse a página principal**
2. **Selecione o tipo de QR Code** que deseja criar
3. **Preencha os campos** conforme o tipo selecionado
4. **Configure o tamanho** e nível de correção de erro
5. **Clique em "Gerar QR Code"**
6. **Baixe, imprima ou compartilhe** seu QR code

## 📱 Tipos de QR Code

### Texto
Ideal para mensagens simples, notas ou qualquer texto.

### URL/Link
Para direcionar usuários a websites específicos.

### Email
Pré-preenche o aplicativo de email com destinatário, assunto e mensagem.

### Telefone
Inicia uma chamada telefônica automaticamente.

### SMS
Pré-preenche uma mensagem SMS.

### WiFi
Permite conexão automática a redes WiFi sem digitar senhas.

### Cartão de Visita (vCard)
Adiciona contato diretamente à agenda do celular.

## 🔧 Personalização

### Modificar estilos
Edite o arquivo `assets/css/style.css` para personalizar a aparência.

### Adicionar novos tipos
1. Modifique o formulário em `index.php`
2. Atualize a classe `QRCodeGenerator.php`
3. Adicione validação em `assets/js/script.js`

### Configurar API alternativa
Modifique o método `generateQRCode()` na classe `QRCodeGenerator` para usar outras APIs.

## 🛡️ Segurança

- Rate limiting por IP
- Validação de entrada
- Sanitização de dados
- Proteção contra XSS
- Limpeza automática de arquivos
- Logs de atividade

## 📊 Logs

O sistema gera logs em `logs/activity.log` com informações sobre:
- QR codes gerados
- Erros ocorridos
- Tentativas de acesso bloqueadas

## 🚨 Troubleshooting

### QR codes não são gerados
1. Verifique se a extensão GD está ativada
2. Confirme se há acesso à internet
3. Verifique permissões da pasta `qrcodes/`

### Erro 500
1. Verifique os logs do PHP
2. Confirme se todas as dependências estão instaladas
3. Verifique permissões de arquivos e pastas

### QR codes não funcionam
1. Teste com diferentes aplicativos de leitura
2. Verifique se os dados estão corretos
3. Tente um nível de correção de erro mais alto

## 📈 Melhorias Futuras

- [ ] Histórico de QR codes gerados
- [ ] Personalização de cores
- [ ] Logos em QR codes
- [ ] API REST para integração
- [ ] Batch generation (múltiplos QR codes)
- [ ] Analytics de uso

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 🆘 Suporte

Para reportar bugs ou solicitar features:
1. Abra uma issue no GitHub
2. Inclua informações sobre seu ambiente
3. Descreva o problema detalhadamente

## 👨‍💻 Autor

Desenvolvido com ❤️ por [Seu Nome]

---

**🔗 Gerador de QR Code** - Transformando dados em códigos visuais desde 2025!