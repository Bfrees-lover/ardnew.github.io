<?php
// Подключаем файл конфигурации базы данных
require_once 'config/database.php';

try {
    // Проверяем наличие модуля с URL 'modules/arduino-projects.html'
    $stmt = $pdo->prepare("SELECT * FROM modules WHERE url = ?");
    $stmt->execute(['modules/arduino-projects.html']);
    $module = $stmt->fetch();
    
    if ($module) {
        echo "Модуль найден в базе данных:<br>";
        echo "ID: " . $module['id'] . "<br>";
        echo "Название: " . $module['title'] . "<br>";
        echo "Описание: " . $module['description'] . "<br>";
        echo "URL: " . $module['url'] . "<br>";
        echo "Активен: " . ($module['active'] ? 'Да' : 'Нет') . "<br>";
        echo "Порядок сортировки: " . $module['sort_order'] . "<br>";
        echo "Создан: " . $module['created_at'] . "<br>";
        echo "Обновлен: " . $module['updated_at'] . "<br>";
    } else {
        echo "Модуль с URL 'modules/arduino-projects.html' не найден в базе данных.";
    }
} catch (PDOException $e) {
    echo "Ошибка при проверке модуля: " . $e->getMessage();
}
?> 