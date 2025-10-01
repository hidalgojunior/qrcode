<?php
/**
 * Configuração do Banco de Dados
 */

// Carregar configurações do .env se existir
if (file_exists(__DIR__ . '/../.env')) {
    $envFile = file_get_contents(__DIR__ . '/../.env');
    foreach (explode("\n", $envFile) as $line) {
        if (empty($line) || strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Configurações do banco de dados
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'devmenthors');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Configurações de sessão
define('SESSION_LIFETIME', 86400); // 24 horas
define('SESSION_NAME', 'DEVMENTHORS_SESSION');

// Configurações de segurança
define('PASSWORD_COST', 12);
define('TOKEN_LENGTH', 32);

// Configurações de upload
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Diretórios
define('MICROSITE_DIR', __DIR__ . '/../microsites/');
define('QRCODE_DIR', __DIR__ . '/../qrcodes/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// URL base
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/QrCode');

/**
 * Classe Database - Gerencia conexões PDO
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Erro ao conectar ao banco de dados");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            throw new Exception("Erro ao executar query");
        }
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollBack();
    }
}

/**
 * Classe Auth - Gerencia autenticação e autorização
 */
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->startSession();
    }
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            session_name(SESSION_NAME);
            session_start();
        }
    }
    
    public function register($name, $email, $password, $phone = null) {
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido");
        }
        
        // Verificar se email já existe
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($this->db->fetch($sql, [$email])) {
            throw new Exception("Este email já está cadastrado");
        }
        
        // Hash da senha
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_COST]);
        
        // Token de verificação
        $verificationToken = bin2hex(random_bytes(TOKEN_LENGTH));
        
        // Definir plano baseado no email (Super Admin = Enterprise)
        $planId = ($email === 'hidalgojunior@gmail.com') ? 4 : 1; // 4 = Enterprise, 1 = Básico
        
        // Inserir usuário
        $sql = "INSERT INTO users (name, email, password, phone, verification_token, plan_id, email_verified) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        // Super Admin já vem com email verificado
        $emailVerified = ($email === 'hidalgojunior@gmail.com') ? 1 : 0;
        
        try {
            $this->db->query($sql, [$name, $email, $hashedPassword, $phone, $verificationToken, $planId, $emailVerified]);
            $userId = $this->db->lastInsertId();
            
            // Se for Super Admin, criar assinatura vitalícia
            if ($email === 'hidalgojunior@gmail.com') {
                $this->createSuperAdminSubscription($userId);
            }
            
            // TODO: Enviar email de verificação
            
            return $userId;
        } catch (Exception $e) {
            throw new Exception("Erro ao criar conta");
        }
    }
    
    private function createSuperAdminSubscription($userId) {
        // Criar assinatura vitalícia para Super Admin
        $sql = "INSERT INTO subscriptions (user_id, plan_id, status, start_date, end_date) 
                VALUES (?, 4, 'active', NOW(), '2099-12-31 23:59:59')";
        $this->db->query($sql, [$userId]);
        
        // Criar notificação de boas-vindas VIP
        $sql = "INSERT INTO notifications (user_id, type, title, message) 
                VALUES (?, 'system', 'Bem-vindo, Super Admin!', 'Você tem acesso total a todos os recursos do DevMenthors.')";
        $this->db->query($sql, [$userId]);
    }
    
    public function login($email, $password, $remember = false) {
        $sql = "SELECT id, name, email, password, status, email_verified, plan_id 
                FROM users WHERE email = ?";
        
        $user = $this->db->fetch($sql, [$email]);
        
        if (!$user) {
            throw new Exception("Email ou senha incorretos");
        }
        
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Email ou senha incorretos");
        }
        
        if ($user['status'] !== 'active') {
            throw new Exception("Conta desativada. Entre em contato com o suporte.");
        }
        
        // Criar sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['plan_id'] = $user['plan_id'];
        $_SESSION['logged_in'] = true;
        
        // Criar token de sessão
        $token = bin2hex(random_bytes(TOKEN_LENGTH));
        $expiresAt = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);
        
        $sql = "INSERT INTO sessions (user_id, token, ip_address, user_agent, expires_at) 
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $user['id'],
            $token,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $expiresAt
        ]);
        
        // Registrar log
        $this->logAction($user['id'], 'login', 'user', $user['id']);
        
        return true;
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logAction($_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id']);
            
            // Remover sessão do banco
            $sql = "DELETE FROM sessions WHERE user_id = ?";
            $this->db->query($sql, [$_SESSION['user_id']]);
        }
        
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function getUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $sql = "SELECT u.*, p.name as plan_name, p.slug as plan_slug, 
                p.microsites_limit, p.has_watermark, p.custom_domain
                FROM users u
                LEFT JOIN plans p ON u.plan_id = p.id
                WHERE u.id = ?";
        
        return $this->db->fetch($sql, [$_SESSION['user_id']]);
    }
    
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function isSuperAdmin() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $user = $this->getUser();
        return $user && $user['email'] === 'hidalgojunior@gmail.com';
    }
    
    public function requireSuperAdmin() {
        if (!$this->isSuperAdmin()) {
            header('HTTP/1.1 403 Forbidden');
            die('Acesso negado. Apenas Super Admin.');
        }
    }
    
    public function updatePassword($userId, $currentPassword, $newPassword) {
        $sql = "SELECT password FROM users WHERE id = ?";
        $user = $this->db->fetch($sql, [$userId]);
        
        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception("Senha atual incorreta");
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => PASSWORD_COST]);
        
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $this->db->query($sql, [$hashedPassword, $userId]);
        
        return true;
    }
    
    public function requestPasswordReset($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $user = $this->db->fetch($sql, [$email]);
        
        if (!$user) {
            // Não revelar se o email existe ou não
            return true;
        }
        
        $token = bin2hex(random_bytes(TOKEN_LENGTH));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora
        
        $sql = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?";
        $this->db->query($sql, [$token, $expires, $user['id']]);
        
        // TODO: Enviar email com link de reset
        
        return true;
    }
    
    private function logAction($userId, $action, $entityType = null, $entityId = null, $oldValues = null, $newValues = null) {
        $sql = "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $userId,
            $action,
            $entityType,
            $entityId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}

// Funções auxiliares globais
function redirect($url) {
    header("Location: $url");
    exit;
}

function json_response($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function error_response($message, $statusCode = 400) {
    json_response(['success' => false, 'error' => $message], $statusCode);
}

function success_response($data = [], $message = 'Sucesso') {
    json_response(array_merge(['success' => true, 'message' => $message], $data));
}
