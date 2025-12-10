<?php
/**
 * Public Portfolio API
 * Получение проектов и изображений для главной страницы
 */

require_once '../admin/config.php';

header('Content-Type: application/json; charset=UTF-8');

$pdo = getDB();

try {
    // Получаем активные проекты с изображениями
    $projects = $pdo->query("
        SELECT 
            p.id,
            p.name,
            p.sort_order,
            COUNT(pi.id) as image_count
        FROM portfolio_projects p
        LEFT JOIN portfolio_images pi ON p.id = pi.project_id
        WHERE p.is_active = 1
        GROUP BY p.id
        ORDER BY p.sort_order ASC, p.created_at DESC
    ")->fetchAll();
    
    // Для каждого проекта получаем изображения
    $result = [];
    foreach ($projects as $project) {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                filename,
                sort_order
            FROM portfolio_images
            WHERE project_id = ?
            ORDER BY sort_order ASC, created_at ASC
        ");
        $stmt->execute([$project['id']]);
        $images = $stmt->fetchAll();
        
        $result[] = [
            'id' => (int)$project['id'],
            'name' => $project['name'],
            'sort_order' => (int)$project['sort_order'],
            'image_count' => (int)$project['image_count'],
            'images' => array_map(function($img) use ($project) {
                return [
                    'id' => (int)$img['id'],
                    'filename' => $img['filename'],
                    'url' => 'img/portfolio/' . $project['id'] . '/' . $img['filename'],
                    'sort_order' => (int)$img['sort_order']
                ];
            }, $images)
        ];
    }
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}



