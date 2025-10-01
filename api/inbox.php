<?php
/**
 * API para gerenciar inbox
 * Endpoints:
 * - GET ?action=list - Listar emails
 * - GET ?action=view&id=X - Visualizar email
 * - GET ?action=stats - Estatísticas
 * - GET ?action=fetch - Buscar novos emails
 * - POST action=reply - Responder email
 * - POST action=delete - Deletar email
 */

header('Content-Type: application/json');

// Capturar erros e converter para JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../includes/database.php';
    require_once __DIR__ . '/../includes/inbox.php';
    require_once __DIR__ . '/../vendor/autoload.php';

    // Verificar autenticação (simplificado - em produção use sessão real)
    session_start();

    // TODO: Adicionar verificação de autenticação real
    // if (!isset($_SESSION['user_id'])) {
    //     http_response_code(401);
    //     echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    //     exit;
    // }

    $inbox = new InboxManager();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao inicializar: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    exit;
}

try {
    // GET requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'list':
                // Listar emails
                $filters = [
                    'category' => $_GET['category'] ?? '',
                    'status' => $_GET['status'] ?? '',
                    'search' => $_GET['search'] ?? '',
                    'page' => $_GET['page'] ?? 1,
                    'limit' => $_GET['limit'] ?? 20
                ];
                
                $result = $inbox->listEmails($filters);
                
                echo json_encode([
                    'success' => true,
                    'emails' => $result['emails'],
                    'pagination' => $result['pagination']
                ]);
                break;
                
            case 'view':
                // Visualizar email específico
                $id = $_GET['id'] ?? 0;
                
                if (!$id) {
                    echo json_encode(['success' => false, 'error' => 'ID não informado']);
                    exit;
                }
                
                // Marcar como lido
                $inbox->markAsRead($id);
                
                // Buscar email
                $db = Database::getInstance();
                $stmt = $db->prepare("SELECT * FROM inbox_emails WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    echo json_encode(['success' => false, 'error' => 'Email não encontrado']);
                    exit;
                }
                
                $email = $result->fetch_assoc();
                $email['attachments'] = json_decode($email['attachments'], true);
                
                echo json_encode([
                    'success' => true,
                    'email' => $email
                ]);
                break;
                
            case 'stats':
                // Estatísticas
                $stats = $inbox->getStats();
                
                echo json_encode([
                    'success' => true,
                    'stats' => $stats
                ]);
                break;
                
            case 'fetch':
                // Buscar novos emails do servidor
                try {
                    error_log("API Inbox: Iniciando fetch de emails");
                    $result = $inbox->fetchNewEmails(50);
                    error_log("API Inbox: Fetch concluído - " . json_encode($result));
                    
                    $stats = $inbox->getStats();
                    error_log("API Inbox: Stats obtidas - " . json_encode($stats));
                    
                    echo json_encode([
                        'success' => $result['success'],
                        'result' => $result,
                        'stats' => $stats
                    ]);
                } catch (Exception $e) {
                    error_log("API Inbox: Erro no fetch - " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'error' => 'Erro ao buscar emails: ' . $e->getMessage()
                    ]);
                }
                break;
                
            case 'test':
                // Testar conexão IMAP
                $result = $inbox->testConnection();
                
                echo json_encode($result);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'error' => 'Ação não reconhecida'
                ]);
        }
    }
    
    // POST requests
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        switch ($action) {
            case 'reply':
                // Responder email
                $emailId = $input['email_id'] ?? 0;
                $replyBody = $input['reply_body'] ?? '';
                
                if (!$emailId || !$replyBody) {
                    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
                    exit;
                }
                
                $result = $inbox->replyEmail($emailId, $replyBody);
                
                echo json_encode($result);
                break;
                
            case 'delete':
                // Deletar email
                $emailId = $input['email_id'] ?? 0;
                
                if (!$emailId) {
                    echo json_encode(['success' => false, 'error' => 'ID não informado']);
                    exit;
                }
                
                $success = $inbox->deleteEmail($emailId);
                
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Email deletado' : 'Erro ao deletar'
                ]);
                break;
                
            case 'mark_read':
                // Marcar como lido
                $emailId = $input['email_id'] ?? 0;
                
                if (!$emailId) {
                    echo json_encode(['success' => false, 'error' => 'ID não informado']);
                    exit;
                }
                
                $success = $inbox->markAsRead($emailId);
                
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Marcado como lido' : 'Erro'
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'error' => 'Ação não reconhecida'
                ]);
        }
    }
    
    else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Método não permitido'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
