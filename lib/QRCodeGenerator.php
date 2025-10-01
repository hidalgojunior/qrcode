<?php
/**
 * Classe simples para gerar QR Codes usando a API do Google Charts (descontinuada)
 * e uma alternativa usando a API do QR Server
 */
class QRCodeGenerator {
    
    private $size;
    private $errorCorrection;
    private $outputDir;
    
    public function __construct($size = 300, $errorCorrection = 'M') {
        $this->size = $size;
        $this->errorCorrection = $errorCorrection;
        $this->outputDir = __DIR__ . '/../qrcodes/';
        
        // Criar diretório se não existir
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    /**
     * Gera QR Code usando API externa
     */
    public function generateQRCode($data, $filename = null, $bgColor = 'ffffff', $fgColor = '000000', $margin = 1) {
        if (!$filename) {
            $filename = 'qrcode_' . time() . '_' . rand(1000, 9999) . '.png';
        }
        
        $filepath = $this->outputDir . $filename;
        
        // URL da API do QR Server
        $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
        $params = http_build_query([
            'size' => $this->size . 'x' . $this->size,
            'ecc' => $this->errorCorrection,
            'data' => $data,
            'bgcolor' => $bgColor,
            'color' => $fgColor,
            'margin' => $margin
        ]);
        
        $qrUrl = $apiUrl . '?' . $params;
        
        // Baixar a imagem
        $imageData = file_get_contents($qrUrl);
        
        if ($imageData === false) {
            throw new Exception('Erro ao gerar QR Code');
        }
        
        // Salvar a imagem
        if (file_put_contents($filepath, $imageData) === false) {
            throw new Exception('Erro ao salvar QR Code');
        }
        
        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => 'qrcodes/' . $filename,
            'size' => $this->size,
            'data' => $data,
            'bg_color' => $bgColor,
            'fg_color' => $fgColor,
            'margin' => $margin
        ];
    }
    
    /**
     * Formata dados para diferentes tipos de QR Code
     */
    public function formatData($type, $data) {
        switch ($type) {
            case 'text':
                return $data['text_content'];
                
            case 'url':
                $url = $data['url_content'];
                if (!preg_match('/^https?:\/\//', $url)) {
                    $url = 'http://' . $url;
                }
                return $url;
                
            case 'email':
                $email = 'mailto:' . $data['email_address'];
                if (!empty($data['email_subject']) || !empty($data['email_body'])) {
                    $params = [];
                    if (!empty($data['email_subject'])) {
                        $params['subject'] = $data['email_subject'];
                    }
                    if (!empty($data['email_body'])) {
                        $params['body'] = $data['email_body'];
                    }
                    $email .= '?' . http_build_query($params);
                }
                return $email;
                
            case 'phone':
                return 'tel:' . $data['phone_number'];
                
            case 'sms':
                $sms = 'sms:' . $data['sms_number'];
                if (!empty($data['sms_message'])) {
                    $sms .= '?body=' . urlencode($data['sms_message']);
                }
                return $sms;
                
            case 'wifi':
                $security = $data['wifi_security'];
                $password = isset($data['wifi_password']) ? $data['wifi_password'] : '';
                $hidden = 'false'; // Pode ser implementado posteriormente
                
                return sprintf(
                    'WIFI:T:%s;S:%s;P:%s;H:%s;;',
                    $security,
                    $data['wifi_ssid'],
                    $password,
                    $hidden
                );
                
            case 'vcard':
                $vcard = "BEGIN:VCARD\n";
                $vcard .= "VERSION:3.0\n";
                
                if (!empty($data['vcard_name'])) {
                    $vcard .= "FN:" . $data['vcard_name'] . "\n";
                    // Adicionar N (nome estruturado) se possível
                    $nameParts = explode(' ', $data['vcard_name'], 2);
                    if (count($nameParts) == 2) {
                        $vcard .= "N:" . $nameParts[1] . ";" . $nameParts[0] . ";;;\n";
                    } else {
                        $vcard .= "N:" . $data['vcard_name'] . ";;;;\n";
                    }
                }
                
                if (!empty($data['vcard_organization'])) {
                    $vcard .= "ORG:" . $data['vcard_organization'] . "\n";
                }
                
                if (!empty($data['vcard_title'])) {
                    $vcard .= "TITLE:" . $data['vcard_title'] . "\n";
                }
                
                if (!empty($data['vcard_phone'])) {
                    // Limpar o telefone e adicionar formatação correta
                    $phone = preg_replace('/[^0-9+]/', '', $data['vcard_phone']);
                    $vcard .= "TEL;TYPE=CELL:" . $phone . "\n";
                }
                
                if (!empty($data['vcard_email'])) {
                    $vcard .= "EMAIL;TYPE=INTERNET:" . $data['vcard_email'] . "\n";
                }
                
                if (!empty($data['vcard_website'])) {
                    $website = $data['vcard_website'];
                    if (!preg_match('/^https?:\/\//', $website)) {
                        $website = 'http://' . $website;
                    }
                    $vcard .= "URL:" . $website . "\n";
                }
                
                if (!empty($data['vcard_instagram'])) {
                    $instagram = $data['vcard_instagram'];
                    // Se começar com @, converter para URL completa
                    if (strpos($instagram, '@') === 0) {
                        $instagram = 'https://instagram.com/' . substr($instagram, 1);
                    }
                    // Se não for uma URL completa, assumir que é username e adicionar a URL
                    if (!preg_match('/^https?:\/\//', $instagram)) {
                        $instagram = 'https://instagram.com/' . $instagram;
                    }
                    $vcard .= "URL:" . $instagram . "\n";
                }
                
                $vcard .= "END:VCARD";
                
                return $vcard;
                
            default:
                return $data['text_content'];
        }
    }
    
    /**
     * Limpa arquivos QR Code antigos (mais de 24 horas)
     */
    public function cleanOldFiles() {
        $files = glob($this->outputDir . '*.png');
        $now = time();
        
        foreach ($files as $file) {
            if ($now - filemtime($file) > 24 * 3600) { // 24 horas
                unlink($file);
            }
        }
    }
    
    /**
     * Valida se os dados são válidos para o tipo especificado
     */
    public function validateData($type, $data) {
        switch ($type) {
            case 'text':
                return !empty($data['text_content']);
                
            case 'url':
                return !empty($data['url_content']);
                
            case 'email':
                return !empty($data['email_address']) && filter_var($data['email_address'], FILTER_VALIDATE_EMAIL);
                
            case 'phone':
                return !empty($data['phone_number']);
                
            case 'sms':
                return !empty($data['sms_number']);
                
            case 'wifi':
                return !empty($data['wifi_ssid']);
                
            case 'vcard':
                return !empty($data['vcard_name']);
                
            default:
                return false;
        }
    }
    
    /**
     * Retorna informações sobre o QR Code gerado
     */
    public function getQRInfo($type, $data) {
        $info = [
            'type' => $type,
            'type_name' => $this->getTypeName($type),
            'data' => $this->formatData($type, $data),
            'size' => $this->size . 'x' . $this->size,
            'error_correction' => $this->getErrorCorrectionName($this->errorCorrection)
        ];
        
        return $info;
    }
    
    private function getTypeName($type) {
        $types = [
            'text' => 'Texto',
            'url' => 'URL/Link',
            'email' => 'Email',
            'phone' => 'Telefone',
            'sms' => 'SMS',
            'wifi' => 'WiFi',
            'vcard' => 'Cartão de Visita'
        ];
        
        return isset($types[$type]) ? $types[$type] : 'Desconhecido';
    }
    
    private function getErrorCorrectionName($level) {
        $levels = [
            'L' => 'Baixo (~7%)',
            'M' => 'Médio (~15%)',
            'Q' => 'Alto (~25%)',
            'H' => 'Muito Alto (~30%)'
        ];
        
        return isset($levels[$level]) ? $levels[$level] : 'Médio';
    }
}