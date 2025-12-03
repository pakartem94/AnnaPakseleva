<?php
require_once 'config.php';
requireLogin();

$pdo = getDB();
$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM portfolio_projects WHERE id = ?")->execute([$id]);
            $message = 'Проект удалён';
            $messageType = 'success';
        }
    }
}

$projects = $pdo->query("SELECT * FROM portfolio_projects ORDER BY sort_order ASC, created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Управление портфолио</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Управление портфолио</h1>
                <button class="admin-btn" onclick="openProjectModal()">Добавить проект</button>
            </div>
            
            <?php if ($message): ?>
                <div class="admin-alert admin-alert--<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <div class="admin-section">
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Папка</th>
                                <th>Активен</th>
                                <th>Порядок</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projects)): ?>
                                <tr>
                                    <td colspan="5" class="admin-table__empty">Нет проектов</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($project['name']) ?></td>
                                        <td><?= htmlspecialchars($project['folder']) ?></td>
                                        <td><?= $project['is_active'] ? 'Да' : 'Нет' ?></td>
                                        <td><?= $project['sort_order'] ?></td>
                                        <td>
                                            <button class="admin-btn admin-btn--sm" onclick="editProject(<?= $project['id'] ?>)">Редактировать</button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить проект?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $project['id'] ?>">
                                                <button type="submit" class="admin-btn admin-btn--sm admin-btn--danger">Удалить</button>
                                            </form>
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
    
    <!-- Project Modal -->
    <div id="projectModal" class="modal" style="display: none;">
        <div class="modal__overlay" onclick="closeProjectModal()"></div>
        <div class="modal__content">
            <button class="modal__close" onclick="closeProjectModal()">×</button>
            <div class="modal__body">
                <h3 id="modalTitle">Добавить проект</h3>
                <form id="projectForm" class="admin-form">
                    <input type="hidden" id="projectId" name="id">
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="projectName">Название проекта *</label>
                        <input type="text" class="admin-form__input" id="projectName" name="name" required placeholder="Проект 1">
                    </div>
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="projectFolder">Номер папки *</label>
                        <input type="text" class="admin-form__input" id="projectFolder" name="folder" required placeholder="1" pattern="[0-9]+">
                        <small style="color: var(--color-text-muted);">Номер папки в img/portfolio/ (например: 1, 2, 3)</small>
                    </div>
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="projectSortOrder">Порядок сортировки</label>
                        <input type="number" class="admin-form__input" id="projectSortOrder" name="sort_order" value="0">
                    </div>
                    
                    <div class="admin-form__group">
                        <label>
                            <input type="checkbox" id="projectActive" name="is_active" checked>
                            Активен
                        </label>
                    </div>
                    
                    <div class="admin-form__actions">
                        <button type="button" class="admin-btn admin-btn--secondary" onclick="closeProjectModal()">Отмена</button>
                        <button type="submit" class="admin-btn">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="admin.js"></script>
    <script>
        async function openProjectModal(id = null) {
            const modal = document.getElementById('projectModal');
            const form = document.getElementById('projectForm');
            const title = document.getElementById('modalTitle');
            
            if (id) {
                title.textContent = 'Редактировать проект';
                const response = await fetch(`api/portfolio.php?id=${id}`);
                const project = await response.json();
                
                document.getElementById('projectId').value = project.id;
                document.getElementById('projectName').value = project.name;
                document.getElementById('projectFolder').value = project.folder;
                document.getElementById('projectSortOrder').value = project.sort_order;
                document.getElementById('projectActive').checked = project.is_active == 1;
            } else {
                title.textContent = 'Добавить проект';
                form.reset();
                document.getElementById('projectId').value = '';
            }
            
            modal.style.display = 'flex';
        }
        
        function closeProjectModal() {
            document.getElementById('projectModal').style.display = 'none';
        }
        
        function editProject(id) {
            openProjectModal(id);
        }
        
        document.getElementById('projectForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = {
                id: formData.get('id') || null,
                name: formData.get('name'),
                folder: formData.get('folder'),
                sort_order: parseInt(formData.get('sort_order')) || 0,
                is_active: formData.get('is_active') ? 1 : 0
            };
            
            const method = data.id ? 'PUT' : 'POST';
            const url = 'api/portfolio.php';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.error || 'Ошибка при сохранении');
                }
            } catch (error) {
                alert('Ошибка: ' + error.message);
            }
        });
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
            max-width: 600px;
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
    </style>
</body>
</html>

