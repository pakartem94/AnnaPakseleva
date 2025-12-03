<?php
/**
 * Скрипт для создания/сброса пароля админа
 * УДАЛИТЕ ЭТОТ ФАЙЛ ПОСЛЕ ИСПОЛЬЗОВАНИЯ!
 */

require_once 'config.php';

// Проверка безопасности - только через GET параметр
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    die('Для безопасности добавьте ?confirm=yes в URL');
}

$username = 'admin';
$password = 'admin123';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #5A1D33; }
        .success { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 4px; 
            margin: 20px 0;
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 4px; 
            margin: 20px 0;
        }
        .info {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #5A1D33;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #7C2946;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Сброс пароля администратора</h1>
        
<?php
try {
    $pdo = getDB();
    
    // Хешируем пароль
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Проверяем, существует ли админ
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Обновляем пароль
        $stmt = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE username = ?");
        $stmt->execute([$passwordHash, $username]);
        echo '<div class="success">';
        echo '<strong>✓ Пароль успешно обновлён!</strong><br><br>';
        echo '<strong>Логин:</strong> ' . htmlspecialchars($username) . '<br>';
        echo '<strong>Пароль:</strong> ' . htmlspecialchars($password);
        echo '</div>';
    } else {
        // Создаём нового админа
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$username, $passwordHash]);
        echo '<div class="success">';
        echo '<strong>✓ Администратор успешно создан!</strong><br><br>';
        echo '<strong>Логин:</strong> ' . htmlspecialchars($username) . '<br>';
        echo '<strong>Пароль:</strong> ' . htmlspecialchars($password);
        echo '</div>';
    }
    
    echo '<div class="info">';
    echo '<strong>⚠️ ВАЖНО:</strong> Удалите файл reset-password.php после использования!';
    echo '</div>';
    
    echo '<a href="login.php" class="btn">Перейти на страницу входа</a>';
    
} catch (Exception $e) {
    echo '<div class="error">';
    echo '<strong>Ошибка:</strong> ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
    </div>
</body>
</html>

