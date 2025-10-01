<?php
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Método não permitido', 405);
}

try {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        error_response('Email e senha são obrigatórios');
    }
    
    $auth = new Auth();
    $auth->login($email, $password, $remember);
    
    success_response(['redirect' => 'dashboard/'], 'Login realizado com sucesso');
    
} catch (Exception $e) {
    error_response($e->getMessage());
}
