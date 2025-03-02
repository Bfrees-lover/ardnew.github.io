<?php
// Подключаем файл с настройками базы данных
require_once '../config/database.php';

// Устанавливаем заголовок для JSON
header('Content-Type: application/json');

try {
    // Запрос на получение всех активных модулей
    $stmt = $pdo->prepare("SELECT id, title, description, url FROM modules WHERE active = 1 ORDER BY sort_order");
    $stmt->execute();
    
    // Получаем все модули в виде массива
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Возвращаем модули в формате JSON
    echo json_encode($modules);
} catch (PDOException $e) {
    // В случае ошибки возвращаем сообщение об ошибке
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка при получении модулей: ' . $e->getMessage()]);
}
?> 