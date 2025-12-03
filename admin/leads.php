<?php
require_once 'config.php';
requireLogin();

$pdo = getDB();
$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $status = sanitize($_POST['status'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        
        if ($id) {
            $pdo->prepare("UPDATE leads SET status = ?, notes = ? WHERE id = ?")->execute([$status, $notes, $id]);
            $message = 'Заявка обновлена';
            $messageType = 'success';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM leads WHERE id = ?")->execute([$id]);
            $message = 'Заявка удалена';
            $messageType = 'success';
        }
    }
}

// Get filter
$statusFilter = $_GET['status'] ?? 'all';
$statusFilterSQL = $statusFilter !== 'all' ? "WHERE status = " . $pdo->quote($statusFilter) : '';

// Get leads
$leads = $pdo->query("SELECT * FROM leads $statusFilterSQL ORDER BY created_at DESC")->fetchAll();

// Get statistics
$stats = [
    'all' => $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn(),
    'new' => $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn(),
    'contacted' => $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'contacted'")->fetchColumn(),
    'in_progress' => $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'in_progress'")->fetchColumn(),
    'completed' => $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'completed'")->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Управление заявками</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Управление заявками</h1>
            </div>
            
            <?php if ($message): ?>
                <div class="admin-alert admin-alert--<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <!-- Filters -->
            <div class="admin-section">
                <div style="display: flex; gap: var(--space-md); flex-wrap: wrap;">
                    <a href="?status=all" class="admin-btn <?= $statusFilter === 'all' ? 'admin-btn--active' : 'admin-btn--secondary' ?>">
                        Все (<?= $stats['all'] ?>)
                    </a>
                    <a href="?status=new" class="admin-btn <?= $statusFilter === 'new' ? 'admin-btn--active' : 'admin-btn--secondary' ?>">
                        Новые (<?= $stats['new'] ?>)
                    </a>
                    <a href="?status=contacted" class="admin-btn <?= $statusFilter === 'contacted' ? 'admin-btn--active' : 'admin-btn--secondary' ?>">
                        Связались (<?= $stats['contacted'] ?>)
                    </a>
                    <a href="?status=in_progress" class="admin-btn <?= $statusFilter === 'in_progress' ? 'admin-btn--active' : 'admin-btn--secondary' ?>">
                        В работе (<?= $stats['in_progress'] ?>)
                    </a>
                    <a href="?status=completed" class="admin-btn <?= $statusFilter === 'completed' ? 'admin-btn--active' : 'admin-btn--secondary' ?>">
                        Завершены (<?= $stats['completed'] ?>)
                    </a>
                </div>
            </div>
            
            <!-- Leads Table -->
            <div class="admin-section">
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Имя</th>
                                <th>Телефон</th>
                                <th>Тип</th>
                                <th>Тариф</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leads)): ?>
                                <tr>
                                    <td colspan="7" class="admin-table__empty">Нет заявок</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leads as $lead): ?>
                                    <tr>
                                        <td><?= date('d.m.Y H:i', strtotime($lead['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($lead['name']) ?></td>
                                        <td><a href="tel:<?= htmlspecialchars($lead['phone']) ?>"><?= htmlspecialchars($lead['phone']) ?></a></td>
                                        <td><?= htmlspecialchars($lead['type']) ?></td>
                                        <td><?= htmlspecialchars($lead['tariff'] ?: '-') ?></td>
                                        <td>
                                            <span class="admin-badge admin-badge--<?= $lead['status'] ?>">
                                                <?= htmlspecialchars($lead['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="admin-btn admin-btn--sm" onclick="openLeadModal(<?= $lead['id'] ?>)">Открыть</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Lead Modal -->
    <div id="leadModal" class="modal" style="display: none;">
        <div class="modal__overlay" onclick="closeLeadModal()"></div>
        <div class="modal__content" style="max-width: 800px;">
            <button class="modal__close" onclick="closeLeadModal()">×</button>
            <div class="modal__body">
                <h3>Детали заявки</h3>
                <div id="leadContent">
                    <p>Загрузка...</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="admin.js"></script>
    <script>
        async function openLeadModal(id) {
            const modal = document.getElementById('leadModal');
            const content = document.getElementById('leadContent');
            
            try {
                const response = await fetch(`api/leads.php?id=${id}`);
                const lead = await response.json();
                
                const statusLabels = {
                    'new': 'Новая',
                    'contacted': 'Связались',
                    'in_progress': 'В работе',
                    'completed': 'Завершена',
                    'rejected': 'Отклонена'
                };
                
                content.innerHTML = `
                    <form id="leadForm" class="admin-form" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="${lead.id}">
                        
                        <div class="admin-form__group">
                            <label class="admin-form__label">Дата создания</label>
                            <div>${new Date(lead.created_at).toLocaleString('ru-RU')}</div>
                        </div>
                        
                        <div class="admin-form__group">
                            <label class="admin-form__label">Имя</label>
                            <div>${lead.name}</div>
                        </div>
                        
                        <div class="admin-form__group">
                            <label class="admin-form__label">Телефон</label>
                            <div><a href="tel:${lead.phone}">${lead.phone}</a></div>
                        </div>
                        
                        <div class="admin-form__group">
                            <label class="admin-form__label">Тип заявки</label>
                            <div>${lead.type}</div>
                        </div>
                        
                        ${lead.tariff ? `<div class="admin-form__group">
                            <label class="admin-form__label">Тариф</label>
                            <div>${lead.tariff}</div>
                        </div>` : ''}
                        
                        ${lead.object_type ? `<div class="admin-form__group">
                            <label class="admin-form__label">Тип объекта</label>
                            <div>${lead.object_type}</div>
                        </div>` : ''}
                        
                        ${lead.area ? `<div class="admin-form__group">
                            <label class="admin-form__label">Площадь</label>
                            <div>${lead.area} м²</div>
                        </div>` : ''}
                        
                        ${lead.comment ? `<div class="admin-form__group">
                            <label class="admin-form__label">Комментарий</label>
                            <div>${lead.comment}</div>
                        </div>` : ''}
                        
                        <div class="admin-form__group">
                            <label class="admin-form__label" for="leadStatus">Статус *</label>
                            <select class="admin-form__select" id="leadStatus" name="status" required>
                                <option value="new" ${lead.status === 'new' ? 'selected' : ''}>Новая</option>
                                <option value="contacted" ${lead.status === 'contacted' ? 'selected' : ''}>Связались</option>
                                <option value="in_progress" ${lead.status === 'in_progress' ? 'selected' : ''}>В работе</option>
                                <option value="completed" ${lead.status === 'completed' ? 'selected' : ''}>Завершена</option>
                                <option value="rejected" ${lead.status === 'rejected' ? 'selected' : ''}>Отклонена</option>
                            </select>
                        </div>
                        
                        <div class="admin-form__group">
                            <label class="admin-form__label" for="leadNotes">Заметки</label>
                            <textarea class="admin-form__textarea" id="leadNotes" name="notes" rows="4">${lead.notes || ''}</textarea>
                        </div>
                        
                        <div class="admin-form__actions">
                            <button type="button" class="admin-btn admin-btn--danger" onclick="deleteLead(${lead.id})">Удалить</button>
                            <button type="button" class="admin-btn admin-btn--secondary" onclick="closeLeadModal()">Закрыть</button>
                            <button type="submit" class="admin-btn">Сохранить</button>
                        </div>
                    </form>
                `;
                
                modal.style.display = 'flex';
            } catch (error) {
                alert('Ошибка загрузки: ' + error.message);
            }
        }
        
        function closeLeadModal() {
            document.getElementById('leadModal').style.display = 'none';
        }
        
        function deleteLead(id) {
            if (confirm('Удалить заявку?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    <style>
        .modal {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.5);
        }
        .modal__content {
            background: var(--color-bg-card);
            border-radius: var(--radius-xl);
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        .modal__close {
            position: absolute;
            top: var(--space-md);
            right: var(--space-md);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-bg-main);
            border: none;
            cursor: pointer;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-btn--active {
            background: var(--color-primary-soft);
        }
    </style>
</body>
</html>

