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
    
    // Проверяем, существует ли модуль с URL arduino-boards.html
    $stmt = $pdo->prepare("SELECT id, title, description, url, active, sort_order FROM modules WHERE url = ?");
    $stmt->execute(['modules/arduino-boards.html']);
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
    
    // Выводим все модули для проверки
    echo "<br><br>Список всех модулей в базе данных:<br>";
    $allModules = $pdo->query("SELECT id, title, url FROM modules ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($allModules as $mod) {
        echo "ID: " . $mod['id'] . ", Название: " . $mod['title'] . ", URL: " . $mod['url'] . "<br>";
    }
} catch (PDOException $e) {
    echo "Ошибка при проверке модуля: " . $e->getMessage();
}
?> 