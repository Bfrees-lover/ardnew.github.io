<?php
// Подключаем файл с настройками базы данных
require_once '../config/database.php';

try {
    // Проверяем, существует ли модуль с URL arduino-boards.html
    $stmt = $pdo->prepare("SELECT id, title, description, url, active, sort_order FROM modules WHERE url = ?");
    $stmt->execute(['arduino-boards.html']);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($module) {
        echo "Модуль 'Платы Arduino' найден в базе данных:<br>";
        echo "ID: " . $module['id'] . "<br>";
        echo "Название: " . $module['title'] . "<br>";
        echo "Описание: " . $module['description'] . "<br>";
        echo "URL: " . $module['url'] . "<br>";
        echo "Активен: " . ($module['active'] ? 'Да' : 'Нет') . "<br>";
        echo "Порядок сортировки: " . $module['sort_order'] . "<br>";
    } else {
        echo "Модуль 'Платы Arduino' не найден в базе данных.";
    }
} catch (PDOException $e) {
    echo "Ошибка при проверке модуля: " . $e->getMessage();
}
?> 