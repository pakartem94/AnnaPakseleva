<?php
/**
 * Form Handler
 * Anna Pakseleva Design Studio
 */

// Set JSON header early for all responses
header('Content-Type: application/json; charset=UTF-8');

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Configuration
$config = [
    'email_to' => 'your-email@example.com', // Замените на реальный email
    'email_from' => 'noreply@annapakseleva.ru',
    'email_name' => 'Сайт студии Анны Пакселевой',
    'db_host' => 'localhost',
    'db_name' => 'annapakseleva',
    'db_user' => 'root',
    'db_pass' => '',
];

// Honeypot check
if (!empty($_POST['website'])) {
    http_response_code(200);
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
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

// Save to database (optional - uncomment if database is configured)
/*
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

    $sql = "INSERT INTO leads (
        created_at, name, phone, messenger, type, tariff, 
        object_type, area, stage, comment,
        utm_source, utm_medium, utm_campaign, ip, user_agent
    ) VALUES (
        :created_at, :name, :phone, :messenger, :type, :tariff,
        :object_type, :area, :stage, :comment,
        :utm_source, :utm_medium, :utm_campaign, :ip, :user_agent
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
    error_log('Database error: ' . $e->getMessage());
}
*/

// Save to file as backup
$log_file = __DIR__ . '/../logs/leads.log';
$log_dir = dirname($log_file);

if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

$log_entry = date('Y-m-d H:i:s') . ' | ' . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

// Response
echo json_encode([
    'success' => true,
    'message' => 'Заявка успешно отправлена',
], JSON_UNESCAPED_UNICODE);

