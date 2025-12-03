<?php
/**
 * Reviews API
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
                $stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
                $stmt->execute([$id]);
                $review = $stmt->fetch();
                if (!$review) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Отзыв не найден'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                echo json_encode($review, JSON_UNESCAPED_UNICODE);
            } else {
                $reviews = $pdo->query("SELECT * FROM reviews ORDER BY sort_order ASC, created_at DESC")->fetchAll();
                echo json_encode($reviews, JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $name = sanitize($data['name'] ?? '');
            $project = sanitize($data['project'] ?? '');
            $text = sanitize($data['text'] ?? '');
            $rating = (int)($data['rating'] ?? 5);
            $is_published = isset($data['is_published']) ? (int)$data['is_published'] : 1;
            $sort_order = (int)($data['sort_order'] ?? 0);
            
            if (empty($name) || empty($project) || empty($text)) {
                http_response_code(400);
                echo json_encode(['error' => 'Заполните все обязательные поля'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $avatar_initials = getInitials($name);
            
            $stmt = $pdo->prepare("
                INSERT INTO reviews (name, project, text, rating, avatar_initials, is_published, sort_order, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $project, $text, $rating, $avatar_initials, $is_published, $sort_order]);
            
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
            $project = sanitize($data['project'] ?? '');
            $text = sanitize($data['text'] ?? '');
            $rating = (int)($data['rating'] ?? 5);
            $is_published = isset($data['is_published']) ? (int)$data['is_published'] : 1;
            $sort_order = (int)($data['sort_order'] ?? 0);
            
            if (empty($name) || empty($project) || empty($text)) {
                http_response_code(400);
                echo json_encode(['error' => 'Заполните все обязательные поля'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $avatar_initials = getInitials($name);
            
            $stmt = $pdo->prepare("
                UPDATE reviews 
                SET name = ?, project = ?, text = ?, rating = ?, avatar_initials = ?, 
                    is_published = ?, sort_order = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$name, $project, $text, $rating, $avatar_initials, $is_published, $sort_order, $id]);
            
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'DELETE':
            $id = (int)($_GET['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID не указан'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
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

