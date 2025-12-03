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
            $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);
            $message = 'Отзыв удалён';
            $messageType = 'success';
        }
    }
}

$reviews = $pdo->query("SELECT * FROM reviews ORDER BY sort_order ASC, created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Управление отзывами</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Управление отзывами</h1>
                <button class="admin-btn" onclick="openReviewModal()">Добавить отзыв</button>
            </div>
            
            <?php if ($message): ?>
                <div class="admin-alert admin-alert--<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <div class="admin-section">
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Проект</th>
                                <th>Рейтинг</th>
                                <th>Опубликован</th>
                                <th>Порядок</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reviews)): ?>
                                <tr>
                                    <td colspan="6" class="admin-table__empty">Нет отзывов</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($review['name']) ?></td>
                                        <td><?= htmlspecialchars($review['project']) ?></td>
                                        <td><?= str_repeat('★', $review['rating']) ?></td>
                                        <td><?= $review['is_published'] ? 'Да' : 'Нет' ?></td>
                                        <td><?= $review['sort_order'] ?></td>
                                        <td>
                                            <button class="admin-btn admin-btn--sm" onclick="editReview(<?= $review['id'] ?>)">Редактировать</button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить отзыв?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $review['id'] ?>">
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
    
    <!-- Review Modal -->
    <div id="reviewModal" class="modal" style="display: none;">
        <div class="modal__overlay" onclick="closeReviewModal()"></div>
        <div class="modal__content">
            <button class="modal__close" onclick="closeReviewModal()">×</button>
            <div class="modal__body">
                <h3 id="modalTitle">Добавить отзыв</h3>
                <form id="reviewForm" class="admin-form">
                    <input type="hidden" id="reviewId" name="id">
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="reviewName">Имя *</label>
                        <input type="text" class="admin-form__input" id="reviewName" name="name" required>
                    </div>
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="reviewProject">Проект *</label>
                        <input type="text" class="admin-form__input" id="reviewProject" name="project" required placeholder="Квартира 85 м²">
                    </div>
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="reviewText">Текст отзыва *</label>
                        <textarea class="admin-form__textarea" id="reviewText" name="text" required></textarea>
                    </div>
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="reviewRating">Рейтинг *</label>
                        <select class="admin-form__select" id="reviewRating" name="rating" required>
                            <option value="5">5 звёзд</option>
                            <option value="4">4 звезды</option>
                            <option value="3">3 звезды</option>
                            <option value="2">2 звезды</option>
                            <option value="1">1 звезда</option>
                        </select>
                    </div>
                    
                    <div class="admin-form__group">
                        <label class="admin-form__label" for="reviewSortOrder">Порядок сортировки</label>
                        <input type="number" class="admin-form__input" id="reviewSortOrder" name="sort_order" value="0">
                    </div>
                    
                    <div class="admin-form__group">
                        <label>
                            <input type="checkbox" id="reviewPublished" name="is_published" checked>
                            Опубликован
                        </label>
                    </div>
                    
                    <div class="admin-form__actions">
                        <button type="button" class="admin-btn admin-btn--secondary" onclick="closeReviewModal()">Отмена</button>
                        <button type="submit" class="admin-btn">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="admin.js"></script>
    <script>
        async function openReviewModal(id = null) {
            const modal = document.getElementById('reviewModal');
            const form = document.getElementById('reviewForm');
            const title = document.getElementById('modalTitle');
            
            if (id) {
                title.textContent = 'Редактировать отзыв';
                const response = await fetch(`api/reviews.php?id=${id}`);
                const review = await response.json();
                
                document.getElementById('reviewId').value = review.id;
                document.getElementById('reviewName').value = review.name;
                document.getElementById('reviewProject').value = review.project;
                document.getElementById('reviewText').value = review.text;
                document.getElementById('reviewRating').value = review.rating;
                document.getElementById('reviewSortOrder').value = review.sort_order;
                document.getElementById('reviewPublished').checked = review.is_published == 1;
            } else {
                title.textContent = 'Добавить отзыв';
                form.reset();
                document.getElementById('reviewId').value = '';
            }
            
            modal.style.display = 'flex';
        }
        
        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
        }
        
        function editReview(id) {
            openReviewModal(id);
        }
        
        document.getElementById('reviewForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = {
                id: formData.get('id') || null,
                name: formData.get('name'),
                project: formData.get('project'),
                text: formData.get('text'),
                rating: parseInt(formData.get('rating')),
                sort_order: parseInt(formData.get('sort_order')) || 0,
                is_published: formData.get('is_published') ? 1 : 0
            };
            
            const method = data.id ? 'PUT' : 'POST';
            const url = 'api/reviews.php';
            
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

