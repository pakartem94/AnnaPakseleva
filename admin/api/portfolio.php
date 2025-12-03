<?php
/**
 * Portfolio API
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
                $stmt = $pdo->prepare("SELECT * FROM portfolio_projects WHERE id = ?");
                $stmt->execute([$id]);
                $project = $stmt->fetch();
                if (!$project) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Проект не найден'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                echo json_encode($project, JSON_UNESCAPED_UNICODE);
            } else {
                $projects = $pdo->query("SELECT * FROM portfolio_projects ORDER BY sort_order ASC, created_at DESC")->fetchAll();
                echo json_encode($projects, JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $name = sanitize($data['name'] ?? '');
            $folder = sanitize($data['folder'] ?? '');
            $is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
            $sort_order = (int)($data['sort_order'] ?? 0);
            
            if (empty($name) || empty($folder)) {
                http_response_code(400);
                echo json_encode(['error' => 'Заполните все обязательные поля'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO portfolio_projects (name, folder, is_active, sort_order, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $folder, $is_active, $sort_order]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = (int)($data['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID не указан'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $name = sanitize($data['name'] ?? '');
            $folder = sanitize($data['folder'] ?? '');
            $is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
            $sort_order = (int)($data['sort_order'] ?? 0);
            
            if (empty($name) || empty($folder)) {
                http_response_code(400);
                echo json_encode(['error' => 'Заполните все обязательные поля'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $stmt = $pdo->prepare("
                UPDATE portfolio_projects 
                SET name = ?, folder = ?, is_active = ?, sort_order = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$name, $folder, $is_active, $sort_order, $id]);
            
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'DELETE':
            $id = (int)($_GET['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID не указан'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM portfolio_projects WHERE id = ?");
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

