<?php
// Подключаем файл с настройками базы данных
require_once '../config/database.php';

// Устанавливаем заголовок для JSON
header('Content-Type: application/json');

// Проверяем, что запрос отправлен методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit;
}

// Получаем данные из тела запроса
$data = json_decode(file_get_contents('php://input'), true);

// Проверяем наличие необходимых полей
if (!isset($data['title']) || !isset($data['description']) || !isset($data['url'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Отсутствуют обязательные поля']);
    exit;
}

// Устанавливаем значения по умолчанию для необязательных полей
$active = isset($data['active']) ? (int)$data['active'] : 1;
$sort_order = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;

try {
    // Подготавливаем запрос на добавление модуля
    $stmt = $pdo->prepare("INSERT INTO modules (title, description, url, active, sort_order) VALUES (?, ?, ?, ?, ?)");
    
    // Выполняем запрос с параметрами
    $stmt->execute([$data['title'], $data['description'], $data['url'], $active, $sort_order]);
    
    // Получаем ID добавленного модуля
    $moduleId = $pdo->lastInsertId();
    
    // Возвращаем успешный ответ
    echo json_encode(['success' => true, 'message' => 'Модуль успешно добавлен', 'id' => $moduleId]);
} catch (PDOException $e) {
    // В случае ошибки возвращаем сообщение об ошибке
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении модуля: ' . $e->getMessage()]);
}
?> 