<?php
// Параметры подключения к базе данных
$host = 'localhost';
$dbname = 'arduino_learn';
$username = 'root';
$password = '';

try {
    // Создаем PDO подключение
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Устанавливаем режим обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Запрос на получение всех активных модулей (как в api/modules.php)
    $stmt = $pdo->prepare("SELECT id, title, description, url FROM modules WHERE active = 1 ORDER BY sort_order");
    $stmt->execute();
    
    // Получаем все модули в виде массива
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Выводим модули в формате JSON
    header('Content-Type: application/json');
    echo json_encode($modules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // В случае ошибки выводим сообщение об ошибке
    echo "Ошибка при получении модулей: " . $e->getMessage();
}
?> 