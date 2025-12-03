<?php
/**
 * Portfolio API
 */

require_once '../config.php';
requireLogin();

header('Content-Type: application/json; charset=UTF-8');

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Загрузка изображений
    if (isset($_GET['upload_images']) && $method === 'POST') {
        $project_id = (int)($_POST['project_id'] ?? 0);
        
        if (!$project_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID проекта не указан'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Абсолютный путь к папке изображений
        $baseDir = dirname(dirname(__DIR__)) . '/img/portfolio/';
        $uploadDir = $baseDir . $project_id . '/';
        
        // Создаём базовую папку, если её нет
        if (!is_dir($baseDir)) {
            if (!mkdir($baseDir, 0755, true)) {
                http_response_code(500);
                echo json_encode(['error' => 'Не удалось создать базовую папку для изображений'], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        
        // Создаём папку проекта, если её нет
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                http_response_code(500);
                echo json_encode(['error' => 'Не удалось создать папку для изображений проекта'], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        
        // Устанавливаем безопасные права на запись
        @chmod($uploadDir, 0755);
        
        if (!is_writable($uploadDir)) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Папка для изображений недоступна для записи',
                'debug' => [
                    'path' => $uploadDir,
                    'exists' => is_dir($uploadDir),
                    'readable' => is_readable($uploadDir),
                    'writable' => is_writable($uploadDir),
                    'perms' => substr(sprintf('%o', fileperms($uploadDir)), -4)
                ]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $uploaded = [];
        $errors = [];
        
        if (isset($_FILES['images'])) {
            $files = $_FILES['images'];
            $sortOrders = $_POST['sort_orders'] ?? [];
            
            // Проверяем, массив ли это файлов или один файл
            if (isset($files['name']) && is_array($files['name'])) {
                for ($i = 0; $i < count($files['name']); $i++) {
                    $originalName = $files['name'][$i];
                    $errorCode = $files['error'][$i];
                    
                    // Проверка ошибок загрузки
                    if ($errorCode !== UPLOAD_ERR_OK) {
                        $errorMessages = [
                            UPLOAD_ERR_INI_SIZE => 'Файл превышает максимальный размер',
                            UPLOAD_ERR_FORM_SIZE => 'Файл превышает максимальный размер формы',
                            UPLOAD_ERR_PARTIAL => 'Файл загружен частично',
                            UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
                            UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
                            UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла на диск',
                            UPLOAD_ERR_EXTENSION => 'Загрузка остановлена расширением'
                        ];
                        $errors[] = "Файл {$originalName}: " . ($errorMessages[$errorCode] ?? "Ошибка загрузки (код: {$errorCode})");
                        continue;
                    }
                    
                    $tmpName = $files['tmp_name'][$i];
                    
                    if (!is_uploaded_file($tmpName)) {
                        $errors[] = "Файл {$originalName}: не является загруженным файлом";
                        continue;
                    }
                    
                    // Проверка размера файла (максимум 10MB)
                    $maxFileSize = 10 * 1024 * 1024; // 10MB
                    if ($files['size'][$i] > $maxFileSize) {
                        $errors[] = "Файл {$originalName}: превышает максимальный размер (10MB)";
                        continue;
                    }
                    
                    // Проверка формата
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $tmpName);
                    finfo_close($finfo);
                    
                    if ($mimeType !== 'image/webp') {
                        $errors[] = "Файл {$originalName}: не является webP изображением (тип: {$mimeType})";
                        continue;
                    }
                    
                    // Генерируем уникальное имя файла
                    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                    if (strtolower($ext) !== 'webp') {
                        $ext = 'webp';
                    }
                    $filename = uniqid() . '.' . $ext;
                    $filepath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($tmpName, $filepath)) {
                        $sortOrder = isset($sortOrders[$i]) ? (int)$sortOrders[$i] : 0;
                        
                        try {
                            $stmt = $pdo->prepare("
                                INSERT INTO portfolio_images (project_id, filename, sort_order, created_at)
                                VALUES (?, ?, ?, NOW())
                            ");
                            $stmt->execute([$project_id, $filename, $sortOrder]);
                            
                            $uploaded[] = [
                                'id' => $pdo->lastInsertId(),
                                'filename' => $filename,
                                'sort_order' => $sortOrder
                            ];
                        } catch (Exception $e) {
                            // Удаляем файл, если не удалось сохранить в БД
                            if (file_exists($filepath)) {
                                unlink($filepath);
                            }
                            $errors[] = "Файл {$originalName}: ошибка сохранения в БД - " . $e->getMessage();
                        }
                    } else {
                        $errors[] = "Файл {$originalName}: ошибка перемещения файла (проверьте права доступа)";
                    }
                }
            } else {
                // Один файл
                if ($files['error'] === UPLOAD_ERR_OK) {
                    $originalName = $files['name'];
                    $tmpName = $files['tmp_name'];
                    
                    // Проверка размера файла (максимум 10MB)
                    $maxFileSize = 10 * 1024 * 1024; // 10MB
                    if ($files['size'] > $maxFileSize) {
                        $errors[] = "Файл {$originalName}: превышает максимальный размер (10MB)";
                    } else {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($finfo, $tmpName);
                        finfo_close($finfo);
                        
                        if ($mimeType === 'image/webp') {
                        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                        if (strtolower($ext) !== 'webp') {
                            $ext = 'webp';
                        }
                        $filename = uniqid() . '.' . $ext;
                        $filepath = $uploadDir . $filename;
                        
                        if (move_uploaded_file($tmpName, $filepath)) {
                            $sortOrder = isset($sortOrders[0]) ? (int)$sortOrders[0] : 0;
                            $stmt = $pdo->prepare("
                                INSERT INTO portfolio_images (project_id, filename, sort_order, created_at)
                                VALUES (?, ?, ?, NOW())
                            ");
                            $stmt->execute([$project_id, $filename, $sortOrder]);
                            $uploaded[] = [
                                'id' => $pdo->lastInsertId(),
                                'filename' => $filename,
                                'sort_order' => $sortOrder
                            ];
                        } else {
                            $errors[] = "Файл {$originalName}: ошибка перемещения файла";
                        }
                    } else {
                        $errors[] = "Файл {$originalName}: не является webP изображением";
                    }
                    }
                }
            }
        }
        
        if (count($errors) > 0) {
            http_response_code(400);
            echo json_encode([
                'success' => count($uploaded) > 0,
                'error' => implode("\n", $errors),
                'uploaded' => $uploaded
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => true, 'uploaded' => $uploaded], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
    
    // Обновление порядка сортировки
    if (isset($_GET['update_sort']) && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $images = $data['images'] ?? [];
        
        foreach ($images as $img) {
            $stmt = $pdo->prepare("UPDATE portfolio_images SET sort_order = ? WHERE id = ?");
            $stmt->execute([$img['sort_order'], $img['id']]);
        }
        
        echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Получение изображений проекта
    if (isset($_GET['images']) && $method === 'GET') {
        $project_id = (int)$_GET['images'];
        $stmt = $pdo->prepare("
            SELECT * FROM portfolio_images 
            WHERE project_id = ? 
            ORDER BY sort_order ASC, created_at ASC
        ");
        $stmt->execute([$project_id]);
        $images = $stmt->fetchAll();
        echo json_encode($images, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Удаление изображения
    if (isset($_GET['image_id']) && $method === 'DELETE') {
        $image_id = (int)$_GET['image_id'];
        
        $stmt = $pdo->prepare("SELECT project_id, filename FROM portfolio_images WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch();
        
        if ($image) {
            $filepath = '../../img/portfolio/' . $image['project_id'] . '/' . $image['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            $stmt = $pdo->prepare("DELETE FROM portfolio_images WHERE id = ?");
            $stmt->execute([$image_id]);
            
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Изображение не найдено'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
    
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM portfolio_projects WHERE id = ?");
                $stmt->execute([$id]);
                $project = $stmt->fetch();
                if (!$project) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Проект не найден'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                echo json_encode($project, JSON_UNESCAPED_UNICODE);
            } else {
                $projects = $pdo->query("SELECT * FROM portfolio_projects ORDER BY sort_order ASC, created_at DESC")->fetchAll();
                echo json_encode($projects, JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $name = sanitize($data['name'] ?? '');
            $is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
            $sort_order = (int)($data['sort_order'] ?? 0);
            
            if (empty($name)) {
                http_response_code(400);
                echo json_encode(['error' => 'Заполните название проекта'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO portfolio_projects (name, is_active, sort_order, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $is_active, $sort_order]);
            
            $projectId = $pdo->lastInsertId();
            
            // Создаём папку для изображений
            $uploadDir = '../../img/portfolio/' . $projectId . '/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
                @chmod($uploadDir, 0755);
            }
            
            echo json_encode(['success' => true, 'id' => $projectId], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = (int)($data['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID не указан'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $name = sanitize($data['name'] ?? '');
            $is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
            $sort_order = (int)($data['sort_order'] ?? 0);
            
            if (empty($name)) {
                http_response_code(400);
                echo json_encode(['error' => 'Заполните название проекта'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $stmt = $pdo->prepare("
                UPDATE portfolio_projects 
                SET name = ?, is_active = ?, sort_order = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$name, $is_active, $sort_order, $id]);
            
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'DELETE':
            $id = (int)($_GET['id'] ?? 0);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID не указан'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            // Получаем абсолютный путь к папке изображений
            $baseDir = dirname(dirname(__DIR__)) . '/img/portfolio/';
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
            $stmt = $pdo->prepare("DELETE FROM portfolio_projects WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    // Логируем ошибку, но не раскрываем детали пользователю
    error_log('Error in portfolio API: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Произошла ошибка при обработке запроса'], JSON_UNESCAPED_UNICODE);
}
