<?php
require_once 'config.php';
requireLogin();

$pdo = getDB();

// Get statistics
$stats = [
    'leads_new' => $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn(),
    'leads_total' => $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn(),
    'reviews_published' => $pdo->query("SELECT COUNT(*) FROM reviews WHERE is_published = 1")->fetchColumn(),
    'reviews_total' => $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
    'portfolio_projects' => $pdo->query("SELECT COUNT(*) FROM portfolio_projects WHERE is_active = 1")->fetchColumn(),
];

// Get recent leads
$recentLeads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Главная</h1>
            </div>
            
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <div class="admin-stat-card__icon" style="background: var(--color-primary);">
                        <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/></svg>
                    </div>
                    <div class="admin-stat-card__content">
                        <div class="admin-stat-card__value"><?= $stats['leads_new'] ?></div>
                        <div class="admin-stat-card__label">Новых заявок</div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="admin-stat-card__icon" style="background: var(--color-accent-blue);">
                        <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/></svg>
                    </div>
                    <div class="admin-stat-card__content">
                        <div class="admin-stat-card__value"><?= $stats['leads_total'] ?></div>
                        <div class="admin-stat-card__label">Всего заявок</div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="admin-stat-card__icon" style="background: var(--color-highlight-gold);">
                        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div class="admin-stat-card__content">
                        <div class="admin-stat-card__value"><?= $stats['reviews_published'] ?></div>
                        <div class="admin-stat-card__label">Опубликованных отзывов</div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="admin-stat-card__icon" style="background: var(--color-accent-greige);">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>
                    </div>
                    <div class="admin-stat-card__content">
                        <div class="admin-stat-card__value"><?= $stats['portfolio_projects'] ?></div>
                        <div class="admin-stat-card__label">Проектов в портфолио</div>
                    </div>
                </div>
            </div>
            
            <div class="admin-section">
                <h2>Последние заявки</h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Имя</th>
                                <th>Телефон</th>
                                <th>Тип</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentLeads)): ?>
                                <tr>
                                    <td colspan="6" class="admin-table__empty">Нет заявок</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentLeads as $lead): ?>
                                    <tr>
                                        <td><?= date('d.m.Y H:i', strtotime($lead['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($lead['name']) ?></td>
                                        <td><?= htmlspecialchars($lead['phone']) ?></td>
                                        <td><?= htmlspecialchars($lead['type']) ?></td>
                                        <td>
                                            <span class="admin-badge admin-badge--<?= $lead['status'] ?>">
                                                <?= htmlspecialchars($lead['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="leads.php?id=<?= $lead['id'] ?>" class="admin-btn admin-btn--sm">Открыть</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: var(--space-lg);">
                    <a href="leads.php" class="btn btn--outline">Все заявки</a>
                </div>
            </div>
        </main>
    </div>
    <script src="admin.js"></script>
</body>
</html>

