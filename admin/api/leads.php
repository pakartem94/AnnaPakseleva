<?php
/**
 * Leads API
 */

require_once '../config.php';
requireLogin();

header('Content-Type: application/json; charset=UTF-8');

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
                $stmt->execute([$id]);
                $lead = $stmt->fetch();
                if (!$lead) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Заявка не найдена'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                echo json_encode($lead, JSON_UNESCAPED_UNICODE);
            } else {
                $status = $_GET['status'] ?? null;
                $limit = (int)($_GET['limit'] ?? 50);
                $offset = (int)($_GET['offset'] ?? 0);
                
                $sql = "SELECT * FROM leads";
                $params = [];
                
                if ($status) {
                    $sql .= " WHERE status = ?";
                    $params[] = $status;
                }
                
                $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $leads = $stmt->fetchAll();
                
                echo json_encode($leads, JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = (int)($data['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID не указан'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $status = sanitize($data['status'] ?? '');
            $notes = sanitize($data['notes'] ?? '');
            
            $stmt = $pdo->prepare("UPDATE leads SET status = ?, notes = ? WHERE id = ?");
            $stmt->execute([$status, $notes, $id]);
            
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'DELETE':
            $id = (int)($_GET['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID не указан'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

