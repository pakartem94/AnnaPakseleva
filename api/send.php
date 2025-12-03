<?php
/**
 * Form Handler
 * Anna Pakseleva Design Studio
 */

// Отключаем вывод ошибок в продакшене (ошибки будут логироваться)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Устанавливаем обработчик ошибок для JSON ответов
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Логируем ошибку
    error_log("PHP Error in send.php: [$errno] $errstr in $errfile on line $errline");
    // Не прерываем выполнение для warning/notice
    return false;
});

// Устанавливаем обработчик критических ошибок
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Критическая ошибка - возвращаем JSON с ошибкой
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Произошла ошибка при обработке запроса'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
});

// Set JSON header early for all responses
header('Content-Type: application/json; charset=UTF-8');

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Configuration - загружаем БД конфиг без сессии и заголовков
define('DB_HOST', 'localhost');
define('DB_NAME', 'pakart06_studio');
define('DB_USER', 'pakart06_studio');
define('DB_PASS', 'IRYtg!RMph4V');

// Get PDO connection (без сессии)
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
            error_log('Database connection failed in send.php: ' . $e->getMessage());
            throw $e; // Пробрасываем исключение для обработки выше
        }
    }
    return $pdo;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Базовая защита от спама (rate limiting через файл)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitFile = __DIR__ . '/../logs/rate_limit_' . md5($ip) . '.tmp';
$rateLimitTime = 60; // 60 секунд
$maxRequests = 5; // максимум 5 запросов в минуту

// Проверка rate limit
$rateLimitData = null;
if (file_exists($rateLimitFile)) {
    $rateLimitData = json_decode(file_get_contents($rateLimitFile), true);
    if ($rateLimitData && (time() - $rateLimitData['time']) < $rateLimitTime) {
        if ($rateLimitData['count'] >= $maxRequests) {
            http_response_code(429);
            echo json_encode(['success' => false, 'error' => 'Слишком много запросов. Попробуйте позже.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $rateLimitData['count']++;
    } else {
        $rateLimitData = ['count' => 1, 'time' => time()];
    }
} else {
    $rateLimitData = ['count' => 1, 'time' => time()];
}

// Сохраняем данные rate limit
@file_put_contents($rateLimitFile, json_encode($rateLimitData), LOCK_EX);

$config = [
    'email_to' => 'ann-ki@mail.ru',
    'email_from' => 'no-reply@studioap.ru',
    'email_name' => 'Сайт студии Анны Пакселевой',
];

// Honeypot check
if (!empty($_POST['website'])) {
    http_response_code(200);
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

// Get form data
$data = [
    'form_type' => sanitize($_POST['form_type'] ?? 'consultation'),
    'name' => sanitize($_POST['name'] ?? ''),
    'phone' => sanitize($_POST['phone'] ?? ''),
    'tariff' => sanitize($_POST['tariff'] ?? ''),
    'object_type' => sanitize($_POST['object_type'] ?? ''),
    'area' => sanitize($_POST['area'] ?? ''),
    'stage' => sanitize($_POST['stage'] ?? ''),
    'comment' => sanitize($_POST['comment'] ?? ''),
    'utm_source' => sanitize($_POST['utm_source'] ?? ''),
    'utm_medium' => sanitize($_POST['utm_medium'] ?? ''),
    'utm_campaign' => sanitize($_POST['utm_campaign'] ?? ''),
    'created_at' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
];

// Validate required fields
if (empty($data['name']) || empty($data['phone'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Заполните обязательные поля'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate privacy agreement (чекбокс может не отправляться, если не отмечен)
if (!isset($_POST['privacy_agree']) || empty($_POST['privacy_agree'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Необходимо согласие с политикой конфиденциальности'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate phone format
$phone_clean = preg_replace('/\D/', '', $data['phone']);
if (strlen($phone_clean) < 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Некорректный номер телефона'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Map values for readability
$object_types = [
    'apartment' => 'Квартира',
    'house' => 'Дом',
    'townhouse' => 'Таунхаус',
    'other' => 'Другое',
];

$stages = [
    'planning' => 'Только планирую ремонт',
    'layout_ready' => 'Уже есть планировка',
    'team_ready' => 'Уже есть бригада',
    'in_progress' => 'Идёт ремонт, нужен контроль',
    'other' => 'Другое',
];

$form_types = [
    'consultation' => 'Консультация',
    'tariff' => 'Заявка на тариф',
];

// Prepare email
$form_type_label = $form_types[$data['form_type']] ?? 'Заявка';

$email_subject = "Новая заявка: {$form_type_label} — {$data['name']}";

$email_body = "
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #5A1D33; font-size: 24px; margin-bottom: 20px; }
        .field { margin-bottom: 15px; padding: 10px; background: #f5f5f5; border-radius: 4px; }
        .label { font-weight: bold; color: #666; font-size: 12px; text-transform: uppercase; }
        .value { font-size: 16px; margin-top: 5px; }
        .utm { color: #999; font-size: 12px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>{$form_type_label}</h1>
        
        <div class='field'>
            <div class='label'>Имя</div>
            <div class='value'>{$data['name']}</div>
        </div>
        
        <div class='field'>
            <div class='label'>Телефон</div>
            <div class='value'>{$data['phone']}</div>
        </div>
";

if (!empty($data['tariff'])) {
    $email_body .= "
        <div class='field'>
            <div class='label'>Тариф</div>
            <div class='value'>{$data['tariff']}</div>
        </div>
    ";
}

if (!empty($data['object_type'])) {
    $object_type_label = $object_types[$data['object_type']] ?? $data['object_type'];
    $email_body .= "
        <div class='field'>
            <div class='label'>Тип объекта</div>
            <div class='value'>{$object_type_label}</div>
        </div>
    ";
}

if (!empty($data['area'])) {
    $email_body .= "
        <div class='field'>
            <div class='label'>Площадь</div>
            <div class='value'>{$data['area']} м²</div>
        </div>
    ";
}

if (!empty($data['stage'])) {
    $stage_label = $stages[$data['stage']] ?? $data['stage'];
    $email_body .= "
        <div class='field'>
            <div class='label'>Этап</div>
            <div class='value'>{$stage_label}</div>
        </div>
    ";
}

if (!empty($data['comment'])) {
    $email_body .= "
        <div class='field'>
            <div class='label'>Комментарий</div>
            <div class='value'>{$data['comment']}</div>
        </div>
    ";
}

$email_body .= "
        <div class='utm'>
            <strong>Дополнительно:</strong><br>
            IP: {$data['ip']}<br>
            Дата: {$data['created_at']}<br>
";

if (!empty($data['utm_source']) || !empty($data['utm_medium']) || !empty($data['utm_campaign'])) {
    $email_body .= "
            UTM Source: {$data['utm_source']}<br>
            UTM Medium: {$data['utm_medium']}<br>
            UTM Campaign: {$data['utm_campaign']}<br>
    ";
}

$email_body .= "
        </div>
    </div>
</body>
</html>
";

// Send email
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    "From: {$config['email_name']} <{$config['email_from']}>",
    "Reply-To: {$data['name']} <{$data['phone']}>",
];

$email_sent = mail(
    $config['email_to'],
    $email_subject,
    $email_body,
    implode("\r\n", $headers)
);

// Save to database
$db_error = false;
try {
    $pdo = getDB();

    $sql = "INSERT INTO leads (
        created_at, name, phone, messenger, type, tariff, 
        object_type, area, stage, comment,
        utm_source, utm_medium, utm_campaign, ip, user_agent, status
    ) VALUES (
        :created_at, :name, :phone, :messenger, :type, :tariff,
        :object_type, :area, :stage, :comment,
        :utm_source, :utm_medium, :utm_campaign, :ip, :user_agent, 'new'
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'created_at' => $data['created_at'],
        'name' => $data['name'],
        'phone' => $data['phone'],
        'messenger' => '',
        'type' => $data['form_type'],
        'tariff' => $data['tariff'],
        'object_type' => $data['object_type'],
        'area' => $data['area'],
        'stage' => $data['stage'],
        'comment' => $data['comment'],
        'utm_source' => $data['utm_source'],
        'utm_medium' => $data['utm_medium'],
        'utm_campaign' => $data['utm_campaign'],
        'ip' => $data['ip'],
        'user_agent' => $data['user_agent'],
    ]);
} catch (PDOException $e) {
    // Логируем ошибку с полной информацией
    $db_error = true;
    error_log('Database error in send.php: ' . $e->getMessage());
    if (isset($sql)) {
        error_log('SQL: ' . $sql);
    }
    error_log('Data: ' . print_r($data, true));
    // Продолжаем выполнение - email уже отправлен, данные сохранены в лог
} catch (Exception $e) {
    // Обработка других исключений (например, ошибка подключения)
    $db_error = true;
    error_log('Error in send.php: ' . $e->getMessage());
    // Продолжаем выполнение
}

// Save to file as backup
$log_file = __DIR__ . '/../logs/leads.log';
$log_dir = dirname($log_file);

if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0750, true);
}

// Устанавливаем безопасные права на файл лога
if (file_exists($log_file)) {
    @chmod($log_file, 0640);
}

$log_entry = date('Y-m-d H:i:s') . ' | ' . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
@file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

// Response (всегда возвращаем успех, даже если БД не сохранила - email отправлен, данные в логе)
echo json_encode([
    'success' => true,
    'message' => 'Заявка успешно отправлена',
], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

