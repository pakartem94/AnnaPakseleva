<?php
require_once 'config.php';
requireLogin();

$pdo = getDB();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'Заполните все поля';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Новые пароли не совпадают';
        $messageType = 'error';
    } elseif (strlen($newPassword) < 6) {
        $message = 'Пароль должен содержать минимум 6 символов';
        $messageType = 'error';
    } else {
        // Проверяем текущий пароль
        $adminId = $_SESSION['admin_id'];
        $stmt = $pdo->prepare("SELECT password_hash FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($currentPassword, $admin['password_hash'])) {
            // Обновляем пароль
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
            $stmt->execute([$newPasswordHash, $adminId]);
            
            $message = 'Пароль успешно изменён';
            $messageType = 'success';
        } else {
            $message = 'Неверный текущий пароль';
            $messageType = 'error';
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
    <title>Смена пароля</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Смена пароля</h1>
            </div>
            
            <?php if ($message): ?>
                <div class="admin-alert admin-alert--<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <div class="admin-section">
                <form method="POST" class="admin-form">
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="currentPassword">Текущий пароль *</label>
                        <input type="password" class="admin-form__input" id="currentPassword" name="current_password" required autofocus>
                    </div>
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="newPassword">Новый пароль *</label>
                        <input type="password" class="admin-form__input" id="newPassword" name="new_password" required minlength="6">
                        <small style="color: var(--color-text-muted);">Минимум 6 символов</small>
                    </div>
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="confirmPassword">Подтвердите новый пароль *</label>
                        <input type="password" class="admin-form__input" id="confirmPassword" name="confirm_password" required minlength="6">
                    </div>
                    
                    <div class="admin-form__actions">
                        <a href="dashboard.php" class="admin-btn admin-btn--secondary">Отмена</a>
                        <button type="submit" class="admin-btn">Изменить пароль</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>


