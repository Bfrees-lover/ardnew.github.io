<?php
// Параметры подключения к базе данных
$host = 'localhost'; // Хост базы данных
$dbname = 'arduino_learn'; // Имя базы данных
$username = 'root'; // Имя пользователя (обычно root для локальной разработки)
$password = ''; // Пароль (обычно пустой для локальной разработки)

try {
    // Создаем PDO подключение
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Устанавливаем режим обработки ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Устанавливаем режим выборки по умолчанию
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // В случае ошибки выводим сообщение
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?> 