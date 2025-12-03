<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Update last login
            $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Вход в админ-панель</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: var(--color-bg-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background: var(--color-bg-card);
            padding: var(--space-xxl);
            border-radius: var(--radius-xl);
            max-width: 400px;
            width: 100%;
            box-shadow: var(--shadow-elevated);
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: var(--space-xl);
            color: var(--color-primary);
        }
        .form__group {
            margin-bottom: var(--space-lg);
        }
        .form__label {
            display: block;
            margin-bottom: var(--space-sm);
            font-weight: 500;
        }
        .form__input {
            width: 100%;
            padding: var(--space-md);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            font-size: var(--font-size-base);
        }
        .form__input:focus {
            outline: none;
            border-color: var(--color-primary);
        }
        .error {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-lg);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Вход в админ-панель</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form__group">
                <label class="form__label" for="username">Логин</label>
                <input type="text" class="form__input" id="username" name="username" required autofocus>
            </div>
            
            <div class="form__group">
                <label class="form__label" for="password">Пароль</label>
                <input type="password" class="form__input" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn--primary btn--full">Войти</button>
        </form>
    </div>
</body>
</html>

