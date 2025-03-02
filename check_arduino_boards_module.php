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
    
    // Запрос на получение модуля "Платы Arduino"
    $stmt = $pdo->prepare("SELECT id, title, description, url, active, sort_order FROM modules WHERE title = ?");
    $stmt->execute(['Платы Arduino']);
    
    // Получаем модуль
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($module) {
        echo "Модуль 'Платы Arduino' найден в базе данных:<br>";
        echo "ID: " . $module['id'] . "<br>";
        echo "Название: " . $module['title'] . "<br>";
        echo "Описание: " . $module['description'] . "<br>";
        echo "URL: " . $module['url'] . "<br>";
        echo "Активен: " . ($module['active'] ? 'Да' : 'Нет') . "<br>";
        echo "Порядок сортировки: " . $module['sort_order'] . "<br>";
        
        // Проверяем, отображается ли модуль на главной странице
        if ($module['active']) {
            echo "<br>Модуль активен и должен отображаться на главной странице сайта.<br>";
            echo "Ссылка на модуль: <a href='modules/" . $module['url'] . "'>modules/" . $module['url'] . "</a>";
        } else {
            echo "<br>Модуль неактивен и не будет отображаться на главной странице сайта.<br>";
            echo "Для отображения модуля на главной странице установите значение active = 1.";
        }
    } else {
        echo "Модуль 'Платы Arduino' не найден в базе данных.";
    }
} catch (PDOException $e) {
    echo "Ошибка при проверке модуля: " . $e->getMessage();
}
?> 