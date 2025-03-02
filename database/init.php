<?php
// Параметры подключения к базе данных
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Подключаемся к серверу MySQL
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Читаем SQL-скрипт
    $sql = file_get_contents('arduino_learn.sql');
    
    // Выполняем SQL-скрипт
    $pdo->exec($sql);
    
    echo "База данных и таблицы успешно созданы!";
} catch (PDOException $e) {
    die("Ошибка при инициализации базы данных: " . $e->getMessage());
}
?> 