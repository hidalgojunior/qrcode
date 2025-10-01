<?php
session_start();

// Verificar parâmetros
$filename = $_GET['file'] ?? '';
$type = $_GET['type'] ?? 'png';

if (empty($filename)) {
    header('HTTP/1.0 404 Not Found');
    exit('Arquivo não encontrado');
}

// Verificar se o arquivo existe
$filepath = __DIR__ . '/qrcodes/' . basename($filename);

if (!file_exists($filepath)) {
    header('HTTP/1.0 404 Not Found');
    exit('Arquivo não encontrado');
}

// Verificar tipo de arquivo solicitado
$allowedTypes = ['png', 'jpg', 'jpeg'];
if (!in_array(strtolower($type), $allowedTypes)) {
    $type = 'png';
}

try {
    // Carregar a imagem
    $image = imagecreatefrompng($filepath);
    
    if ($image === false) {
        throw new Exception('Erro ao carregar a imagem');
    }
    
    // Definir cabeçalhos para download
    $downloadFilename = pathinfo($filename, PATHINFO_FILENAME) . '.' . $type;
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Gerar imagem no formato solicitado
    switch (strtolower($type)) {
        case 'jpg':
        case 'jpeg':
            // Para JPG, criar fundo branco (PNG pode ter transparência)
            $width = imagesx($image);
            $height = imagesy($image);
            $jpgImage = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($jpgImage, 255, 255, 255);
            imagefill($jpgImage, 0, 0, $white);
            imagecopy($jpgImage, $image, 0, 0, 0, 0, $width, $height);
            
            header('Content-Type: image/jpeg');
            imagejpeg($jpgImage, null, 90);
            imagedestroy($jpgImage);
            break;
            
        case 'png':
        default:
            header('Content-Type: image/png');
            imagepng($image);
            break;
    }
    
    imagedestroy($image);
    
} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error');
    exit('Erro ao processar a imagem: ' . $e->getMessage());
}