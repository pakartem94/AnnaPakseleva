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
                <button class="admin-btn" id="addReviewBtn" onclick="openReviewModal(); return false;">Добавить отзыв</button>
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
                                            <button class="admin-btn admin-btn--sm edit-review-btn" data-id="<?= $review['id'] ?>">Редактировать</button>
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
    <div id="reviewModal" class="modal modal--hidden">
        <div class="modal__overlay" id="modalOverlay"></div>
        <div class="modal__content">
            <button class="modal__close" id="modalClose">×</button>
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
                        <button type="button" class="admin-btn admin-btn--secondary" id="cancelReviewBtn">Отмена</button>
                        <button type="submit" class="admin-btn">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="admin.js"></script>
    <script>
        console.log('Скрипт reviews.php загружен');
        
        // Глобальные функции для модального окна
        function openReviewModal(id = null) {
            console.log('openReviewModal вызвана, id:', id);
            const modal = document.getElementById('reviewModal');
            const form = document.getElementById('reviewForm');
            const title = document.getElementById('modalTitle');
            
            if (!modal || !form || !title) {
                console.error('Модальное окно не найдено', {
                    modal: !!modal,
                    form: !!form,
                    title: !!title
                });
                return;
            }
            
            console.log('Модальное окно найдено, текущие стили:', {
                display: window.getComputedStyle(modal).display,
                visibility: window.getComputedStyle(modal).visibility,
                zIndex: window.getComputedStyle(modal).zIndex,
                position: window.getComputedStyle(modal).position
            });
                
            if (id) {
                    title.textContent = 'Редактировать отзыв';
                    fetch(`api/reviews.php?id=${id}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Ошибка загрузки отзыва');
                            }
                            return response.json();
                        })
                        .then(review => {
                            document.getElementById('reviewId').value = review.id;
                            document.getElementById('reviewName').value = review.name || '';
                            document.getElementById('reviewProject').value = review.project || '';
                            document.getElementById('reviewText').value = review.text || '';
                            document.getElementById('reviewRating').value = review.rating || 5;
                            document.getElementById('reviewSortOrder').value = review.sort_order || 0;
                            document.getElementById('reviewPublished').checked = review.is_published == 1;
                            modal.classList.remove('modal--hidden');
                            modal.style.display = 'flex';
                            console.log('Модальное окно открыто (редактирование), display:', modal.style.display);
                        })
                        .catch(error => {
                            console.error('Ошибка загрузки отзыва:', error);
                            alert('Ошибка загрузки отзыва: ' + error.message);
                        });
                } else {
                    title.textContent = 'Добавить отзыв';
                    form.reset();
                    document.getElementById('reviewId').value = '';
                    document.getElementById('reviewPublished').checked = true;
                    document.getElementById('reviewSortOrder').value = '0';
                    document.getElementById('reviewRating').value = '5';
                    modal.classList.remove('modal--hidden');
                    modal.style.display = 'flex';
                    modal.style.visibility = 'visible';
                    modal.style.opacity = '1';
                    modal.style.zIndex = '9999';
                    console.log('Модальное окно открыто (добавление)');
                    console.log('Стили после открытия:', {
                        display: window.getComputedStyle(modal).display,
                        visibility: window.getComputedStyle(modal).visibility,
                        zIndex: window.getComputedStyle(modal).zIndex,
                        position: window.getComputedStyle(modal).position,
                        top: window.getComputedStyle(modal).top,
                        left: window.getComputedStyle(modal).left
                    });
                }
            }
            
        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            if (modal) {
                modal.classList.add('modal--hidden');
                modal.style.display = 'none';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const addBtn = document.getElementById('addReviewBtn');
            const cancelBtn = document.getElementById('cancelReviewBtn');
            const modalOverlay = document.getElementById('modalOverlay');
            const modalClose = document.getElementById('modalClose');
            
            // Обработчики событий
            if (addBtn) {
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openReviewModal();
                });
            } else {
                console.error('Кнопка addReviewBtn не найдена!');
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', closeReviewModal);
            }
            
            if (modalOverlay) {
                modalOverlay.addEventListener('click', closeReviewModal);
            }
            
            if (modalClose) {
                modalClose.addEventListener('click', closeReviewModal);
            }
            
            // Обработчики для кнопок редактирования
            document.querySelectorAll('.edit-review-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    if (id) {
                        openReviewModal(id);
                    }
                });
            });
            
            // Обработчик отправки формы
            const form = document.getElementById('reviewForm');
            if (form) {
                form.addEventListener('submit', async (e) => {
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
            }
        });
    </script>
    <style>
        .modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 99999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(0, 0, 0, 0.5) !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        .modal--hidden {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .modal__overlay {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: rgba(0, 0, 0, 0.5) !important;
            z-index: 1 !important;
        }
        .modal__content {
            background: #ffffff !important;
            background: var(--color-bg-card, #ffffff) !important;
            border-radius: 12px !important;
            border-radius: var(--radius-xl, 12px) !important;
            max-width: 600px !important;
            width: 90% !important;
            max-height: 90vh !important;
            overflow-y: auto !important;
            position: relative !important;
            z-index: 2 !important;
            margin: auto !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
        }
        .modal__body {
            padding: 24px !important;
            padding: var(--space-xl, 24px) !important;
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

