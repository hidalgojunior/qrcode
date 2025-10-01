<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Método não permitido', 405);
}

try {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    
    // Validações
    if (empty($name)) {
        error_response('Nome é obrigatório');
    }
    
    if (empty($email)) {
        error_response('Email é obrigatório');
    }
    
    if (empty($password)) {
        error_response('Senha é obrigatória');
    }
    
    if (strlen($password) < 6) {
        error_response('A senha deve ter no mínimo 6 caracteres');
    }
    
    $auth = new Auth();
    $userId = $auth->register($name, $email, $password, $phone);
    
    // Fazer login automático após registro
    $auth->login($email, $password);
    
    success_response([
        'user_id' => $userId,
        'redirect' => 'dashboard/'
    ], 'Conta criada com sucesso');
    
} catch (Exception $e) {
    error_response($e->getMessage());
}
