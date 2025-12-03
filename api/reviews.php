<?php
/**
 * Public Reviews API
 * Returns published reviews for the main page
 */

header('Content-Type: application/json; charset=UTF-8');

// Database configuration
$config = [
    'db_host' => 'localhost',
    'db_name' => 'pakart06_studio',
    'db_user' => 'pakart06_studio',
    'db_pass' => 'IRYtg!RMph4V',
];

try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    $reviews = $pdo->query("
        SELECT * FROM reviews 
        WHERE is_published = 1 
        ORDER BY sort_order ASC, created_at DESC
    ")->fetchAll();
    
    echo json_encode($reviews, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error'], JSON_UNESCAPED_UNICODE);
}

