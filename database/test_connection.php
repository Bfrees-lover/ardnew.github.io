<?php
// Подключаем файл с настройками базы данных
require_once '../config/database.php';

try {
    // Проверяем подключение, выполняя простой запрос
    $stmt = $pdo->query("SELECT 1");
    
    echo "Подключение к базе данных успешно установлено!";
    
    // Проверяем наличие таблицы modules
    $stmt = $pdo->query("SHOW TABLES LIKE 'modules'");
    if ($stmt->rowCount() > 0) {
        echo "<br>Таблица 'modules' существует.";
        
        // Проверяем количество записей в таблице modules
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM modules");
        $count = $stmt->fetch()['count'];
        echo "<br>Количество модулей в базе данных: $count";
    } else {
        echo "<br>Таблица 'modules' не существует.";
    }
} catch (PDOException $e) {
    die("Ошибка при проверке подключения: " . $e->getMessage());
}
?> 