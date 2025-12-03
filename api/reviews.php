<?php
/**
 * Public Reviews API
 * Returns published reviews for the main page
 */

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../admin/config.php';

try {
    $pdo = getDB();
    
    $reviews = $pdo->query("
        SELECT * FROM reviews 
        WHERE is_published = 1 
        ORDER BY sort_order ASC, created_at DESC
    ")->fetchAll();
    
    echo json_encode($reviews, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // Логируем ошибку, но не раскрываем детали пользователю
    error_log('Database error in reviews.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка загрузки данных'], JSON_UNESCAPED_UNICODE);
}

