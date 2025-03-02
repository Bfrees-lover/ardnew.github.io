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
    
    // Исправляем URL модуля "Платы Arduino"
    $stmt = $pdo->prepare("UPDATE modules SET url = ? WHERE title = ?");
    $stmt->execute(['arduino-boards.html', 'Платы Arduino']);
    
    $rowCount = $stmt->rowCount();
    
    if ($rowCount > 0) {
        echo "URL модуля 'Платы Arduino' успешно исправлен на 'arduino-boards.html'.";
    } else {
        echo "Модуль 'Платы Arduino' не найден в базе данных или URL уже имеет правильное значение.";
    }
} catch (PDOException $e) {
    echo "Ошибка при исправлении URL модуля: " . $e->getMessage();
}
?> 