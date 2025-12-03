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
            // Получаем абсолютный путь к папке изображений
            $baseDir = dirname(__DIR__) . '/img/portfolio/';
            $uploadDir = $baseDir . $id . '/';
            
            // Удаляем все изображения проекта из БД и файлы
            $stmt = $pdo->prepare("SELECT filename FROM portfolio_images WHERE project_id = ?");
            $stmt->execute([$id]);
            $images = $stmt->fetchAll();
            
            foreach ($images as $img) {
                $filepath = $uploadDir . $img['filename'];
                if (file_exists($filepath)) {
                    @unlink($filepath);
                }
            }
            
            // Удаляем все файлы из папки (на случай, если что-то осталось)
            if (is_dir($uploadDir)) {
                $files = glob($uploadDir . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
                
                // Удаляем папку (только если она пустая)
                @rmdir($uploadDir);
            }
            
            // Удаляем проект (изображения удалятся каскадно из БД)
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
                                <th>Изображений</th>
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
                                <?php foreach ($projects as $project): 
                                    $imageCount = $pdo->prepare("SELECT COUNT(*) as count FROM portfolio_images WHERE project_id = ?");
                                    $imageCount->execute([$project['id']]);
                                    $count = $imageCount->fetch()['count'] ?? 0;
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($project['name']) ?></td>
                                        <td><?= $count ?></td>
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
    <div id="projectModal" class="modal modal--hidden">
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
                        <label class="admin-form__label" for="projectSortOrder">Порядок сортировки</label>
                        <input type="number" class="admin-form__input" id="projectSortOrder" name="sort_order" value="0">
                    </div>
                    
                    <div class="admin-form__group">
                        <label>
                            <input type="checkbox" id="projectActive" name="is_active" checked>
                            Активен
                        </label>
                    </div>
                    
                    <div class="admin-form__group" id="imagesGroup" style="display: none;">
                        <label class="admin-form__label">Изображения проекта (webP)</label>
                        <div id="imageUploadArea" class="image-upload-area">
                            <input type="file" id="imageInput" multiple accept="image/webp" style="display: none;">
                            <div class="image-upload-dropzone" id="imageDropzone">
                                <p>Перетащите изображения сюда или <button type="button" class="admin-btn admin-btn--sm" onclick="document.getElementById('imageInput').click()">выберите файлы</button></p>
                                <small>Только формат webP</small>
                            </div>
                            <div id="imagesList" class="images-list"></div>
                        </div>
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
        let currentProjectId = null;
        let images = [];
        
        window.openProjectModal = function(id = null) {
            const modal = document.getElementById('projectModal');
            const form = document.getElementById('projectForm');
            const title = document.getElementById('modalTitle');
            const imagesGroup = document.getElementById('imagesGroup');
            
            if (!modal || !form || !title) {
                return;
            }
            
            currentProjectId = id;
            images = [];
            
            if (id) {
                title.textContent = 'Редактировать проект';
                imagesGroup.style.display = 'block';
                
                Promise.all([
                    fetch(`api/portfolio.php?id=${id}`).then(r => r.json()),
                    fetch(`api/portfolio.php?images=${id}`).then(r => r.json())
                ]).then(([project, projectImages]) => {
                    document.getElementById('projectId').value = project.id;
                    document.getElementById('projectName').value = project.name || '';
                    document.getElementById('projectSortOrder').value = project.sort_order || 0;
                    document.getElementById('projectActive').checked = project.is_active == 1;
                    
                    images = projectImages || [];
                    renderImages();
                    
                    modal.classList.remove('modal--hidden');
                    modal.style.display = 'flex';
                    modal.style.visibility = 'visible';
                    modal.style.opacity = '1';
                }).catch(error => {
                    alert('Ошибка загрузки проекта: ' + error.message);
                });
            } else {
                title.textContent = 'Добавить проект';
                form.reset();
                document.getElementById('projectId').value = '';
                document.getElementById('projectActive').checked = true;
                document.getElementById('projectSortOrder').value = '0';
                imagesGroup.style.display = 'block';
                images = [];
                renderImages();
                
                modal.classList.remove('modal--hidden');
                modal.style.display = 'flex';
                modal.style.visibility = 'visible';
                modal.style.opacity = '1';
            }
        };
        
        function renderImages() {
            const container = document.getElementById('imagesList');
            container.innerHTML = '';
            
            if (images.length === 0) {
                container.innerHTML = '<p style="color: var(--color-text-muted); padding: var(--space-md);">Нет изображений</p>';
                return;
            }
            
            images.forEach((img, index) => {
                const item = document.createElement('div');
                item.className = 'image-item';
                item.draggable = true;
                item.dataset.index = index;
                
                const imgSrc = img.preview || (currentProjectId ? `../img/portfolio/${currentProjectId}/${img.filename}` : '');
                
                item.innerHTML = `
                    <div class="image-item__preview">
                        <img src="${imgSrc}" alt="${img.filename}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22%3E%3C/svg%3E'">
                    </div>
                    <div class="image-item__info">
                        <span>${img.filename}</span>
                        <button type="button" class="admin-btn admin-btn--sm admin-btn--danger" onclick="deleteImage(${img.id || 'null'}, ${index})">Удалить</button>
                    </div>
                    <div class="image-item__drag-handle">☰</div>
                `;
                
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragover', handleDragOver);
                item.addEventListener('drop', handleDrop);
                item.addEventListener('dragend', handleDragEnd);
                
                container.appendChild(item);
            });
        }
        
        let draggedIndex = null;
        
        function handleDragStart(e) {
            draggedIndex = parseInt(e.currentTarget.dataset.index);
            e.currentTarget.classList.add('dragging');
        }
        
        function handleDragOver(e) {
            e.preventDefault();
            const target = e.currentTarget;
            if (target.dataset.index && parseInt(target.dataset.index) !== draggedIndex) {
                target.classList.add('drag-over');
            }
        }
        
        function handleDrop(e) {
            e.preventDefault();
            const target = e.currentTarget;
            const targetIndex = parseInt(target.dataset.index);
            
            if (targetIndex !== draggedIndex && draggedIndex !== null) {
                const dragged = images[draggedIndex];
                images.splice(draggedIndex, 1);
                images.splice(targetIndex, 0, dragged);
                renderImages();
            }
            
            document.querySelectorAll('.image-item').forEach(item => {
                item.classList.remove('drag-over', 'dragging');
            });
        }
        
        function handleDragEnd(e) {
            e.currentTarget.classList.remove('dragging');
            document.querySelectorAll('.image-item').forEach(item => {
                item.classList.remove('drag-over');
            });
        }
        
        window.deleteImage = function(imageId, index) {
            if (!confirm('Удалить изображение?')) return;
            
            if (imageId) {
                fetch(`api/portfolio.php?image_id=${imageId}`, { method: 'DELETE' })
                    .then(r => r.json())
                    .then(result => {
                        if (result.success) {
                            images.splice(index, 1);
                            renderImages();
                        } else {
                            alert('Ошибка удаления: ' + (result.error || 'Неизвестная ошибка'));
                        }
                    });
            } else {
                images.splice(index, 1);
                renderImages();
            }
        };
        
        window.closeProjectModal = function() {
            const modal = document.getElementById('projectModal');
            if (modal) {
                modal.classList.add('modal--hidden');
                modal.style.display = 'none';
                modal.style.visibility = 'hidden';
            }
        };
        
        window.editProject = function(id) {
            openProjectModal(id);
        };
        
        // Обработка загрузки файлов
        const imageInput = document.getElementById('imageInput');
        const imageDropzone = document.getElementById('imageDropzone');
        
        imageInput.addEventListener('change', handleFiles);
        
        imageDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageDropzone.classList.add('drag-over');
        });
        
        imageDropzone.addEventListener('dragleave', () => {
            imageDropzone.classList.remove('drag-over');
        });
        
        imageDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            imageDropzone.classList.remove('drag-over');
            const files = Array.from(e.dataTransfer.files).filter(f => f.type === 'image/webp');
            if (files.length > 0) {
                processFiles(files);
            } else {
                alert('Принимаются только файлы формата webP');
            }
        });
        
        function handleFiles(e) {
            const files = Array.from(e.target.files).filter(f => f.type === 'image/webp');
            if (files.length > 0) {
                processFiles(files);
            } else {
                alert('Принимаются только файлы формата webP');
            }
        }
        
        function processFiles(files) {
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const newImage = {
                        id: null,
                        filename: file.name,
                        sort_order: images.length,
                        file: file,
                        preview: e.target.result
                    };
                    images.push(newImage);
                    renderImages();
                };
                reader.readAsDataURL(file);
            });
        }
        
        document.getElementById('projectForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const projectId = formData.get('id') || null;
            
            // Сначала сохраняем проект
            const data = {
                id: projectId,
                name: formData.get('name'),
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
                
                if (!result.success) {
                    alert(result.error || 'Ошибка при сохранении проекта');
                    return;
                }
                
                const savedProjectId = result.id || data.id;
                
                // Загружаем новые изображения частями (по 20 файлов за раз)
                const newImages = images.filter(img => !img.id && img.file);
                if (newImages.length > 0) {
                    const chunkSize = 20; // Максимум файлов за один запрос (ограничение PHP max_file_uploads)
                    let uploadedCount = 0;
                    let allUploaded = [];
                    
                    for (let i = 0; i < newImages.length; i += chunkSize) {
                        const chunk = newImages.slice(i, i + chunkSize);
                        const uploadFormData = new FormData();
                        
                        chunk.forEach((img, index) => {
                            uploadFormData.append('images[]', img.file);
                            uploadFormData.append('sort_orders[]', img.sort_order);
                        });
                        uploadFormData.append('project_id', savedProjectId);
                        
                        try {
                            const uploadResponse = await fetch('api/portfolio.php?upload_images=1', {
                                method: 'POST',
                                body: uploadFormData
                            });
                            
                            const uploadResult = await uploadResponse.json();
                            if (!uploadResult.success) {
                                const errorMsg = uploadResult.error || 'Неизвестная ошибка';
                                if (uploadedCount > 0) {
                                    alert(`Загружено ${uploadedCount} из ${newImages.length} изображений. Ошибка при загрузке остальных: ${errorMsg}`);
                                } else {
                                    alert('Ошибка загрузки изображений: ' + errorMsg);
                                    return;
                                }
                                break;
                            }
                            
                            if (uploadResult.uploaded) {
                                allUploaded = allUploaded.concat(uploadResult.uploaded);
                            }
                            uploadedCount += chunk.length;
                        } catch (error) {
                            if (uploadedCount > 0) {
                                alert(`Загружено ${uploadedCount} из ${newImages.length} изображений. Ошибка: ${error.message}`);
                            } else {
                                alert('Ошибка загрузки изображений: ' + error.message);
                                return;
                            }
                            break;
                        }
                    }
                    
                    // Обновляем список изображений с ID из базы данных
                    if (allUploaded.length > 0) {
                        const existingImages = images.filter(img => img.id);
                        images = existingImages.concat(allUploaded.map(uploaded => ({
                            id: uploaded.id,
                            filename: uploaded.filename,
                            sort_order: uploaded.sort_order,
                            preview: `../img/portfolio/${savedProjectId}/${uploaded.filename}`
                        })));
                    }
                    
                    if (uploadedCount < newImages.length) {
                        alert(`Загружено ${uploadedCount} из ${newImages.length} изображений. Перезагрузите страницу для проверки.`);
                    }
                }
                
                // Сохраняем порядок сортировки
                if (images.length > 0) {
                    const sortData = images.map((img, index) => ({
                        id: img.id,
                        sort_order: index
                    })).filter(item => item.id);
                    
                    if (sortData.length > 0) {
                        await fetch('api/portfolio.php?update_sort=1', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ images: sortData })
                        });
                    }
                }
                
                location.reload();
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
            visibility: visible;
            opacity: 1;
        }
        .modal--hidden {
            display: none !important;
            visibility: hidden !important;
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
        .image-upload-area {
            margin-top: var(--space-md);
        }
        .image-upload-dropzone {
            border: 2px dashed var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-xl);
            text-align: center;
            background: var(--color-bg-main);
            transition: all var(--transition-fast);
            cursor: pointer;
        }
        .image-upload-dropzone:hover,
        .image-upload-dropzone.drag-over {
            border-color: var(--color-primary);
            background: rgba(90, 29, 51, 0.05);
        }
        .images-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: var(--space-md);
            margin-top: var(--space-lg);
        }
        .image-item {
            position: relative;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            overflow: hidden;
            background: var(--color-bg-card);
            cursor: move;
            transition: all var(--transition-fast);
        }
        .image-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .image-item.dragging {
            opacity: 0.5;
        }
        .image-item.drag-over {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px var(--color-primary);
        }
        .image-item__preview {
            width: 100%;
            padding-top: 100%;
            position: relative;
            background: var(--color-bg-main);
        }
        .image-item__preview img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-item__info {
            padding: var(--space-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--space-xs);
        }
        .image-item__info span {
            font-size: var(--font-size-sm);
            color: var(--color-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }
        .image-item__drag-handle {
            position: absolute;
            top: var(--space-xs);
            right: var(--space-xs);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: var(--space-xs);
            border-radius: var(--radius-sm);
            font-size: 14px;
            cursor: move;
        }
    </style>
</body>
</html>

