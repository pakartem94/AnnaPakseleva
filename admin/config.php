<?php
/**
 * Admin Panel Configuration
 */

// Настройка сессии с безопасными параметрами
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Установить в 1 при использовании HTTPS
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// Запрет индексации админ-панели
header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
// Защита от XSS
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pakart06_studio');
define('DB_USER', 'pakart06_studio');
define('DB_PASS', 'IRYtg!RMph4V');

// Get PDO connection
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            // В продакшене не раскрываем детали ошибки
            error_log('Database connection failed: ' . $e->getMessage());
            die('Database connection failed. Please contact administrator.');
        }
    }
    return $pdo;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Generate avatar initials
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= mb_substr($word, 0, 1, 'UTF-8');
        }
    }
    return mb_strtoupper(mb_substr($initials, 0, 2, 'UTF-8'), 'UTF-8');
}

