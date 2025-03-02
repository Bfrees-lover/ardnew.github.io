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
    
    // Данные для нового модуля
    $moduleData = [
        'title' => 'Платы Arduino',
        'description' => 'Обзор различных плат Arduino, их характеристики и применение',
        'url' => 'modules/arduino-boards.html',
        'active' => 1,
        'sort_order' => 10 // Устанавливаем порядок сортировки после существующих модулей
    ];
    
    // Проверяем, существует ли уже модуль с таким URL
    $checkStmt = $pdo->prepare("SELECT id FROM modules WHERE url = ?");
    $checkStmt->execute([$moduleData['url']]);
    $existingModule = $checkStmt->fetch();
    
    if ($existingModule) {
        echo "Модуль с URL '{$moduleData['url']}' уже существует в базе данных (ID: {$existingModule['id']}).<br>";
        echo "Обновляем информацию о модуле...<br>";
        
        // Обновляем существующий модуль
        $updateStmt = $pdo->prepare("UPDATE modules SET title = ?, description = ?, active = ?, sort_order = ? WHERE id = ?");
        $updateStmt->execute([
            $moduleData['title'],
            $moduleData['description'],
            $moduleData['active'],
            $moduleData['sort_order'],
            $existingModule['id']
        ]);
        
        echo "Модуль успешно обновлен!";
    } else {
        // Добавляем новый модуль
        $insertStmt = $pdo->prepare("INSERT INTO modules (title, description, url, active, sort_order) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->execute([
            $moduleData['title'],
            $moduleData['description'],
            $moduleData['url'],
            $moduleData['active'],
            $moduleData['sort_order']
        ]);
        
        $moduleId = $pdo->lastInsertId();
        echo "Модуль 'Платы Arduino' успешно добавлен в базу данных (ID: {$moduleId})!";
    }
} catch (PDOException $e) {
    echo "Ошибка при добавлении модуля: " . $e->getMessage();
}
?> 