<?php
// Подключаем файл конфигурации базы данных
require_once 'config/database.php';

try {
    // Данные для нового модуля
    $moduleData = [
        'title' => 'Проекты Arduino для начинающих',
        'description' => 'Практические проекты для изучения Arduino с пошаговыми инструкциями',
        'url' => 'modules/arduino-projects.html',
        'active' => 1,
        'sort_order' => 11 // Устанавливаем порядок сортировки после существующих модулей
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
        echo "Модуль 'Проекты Arduino для начинающих' успешно добавлен в базу данных (ID: {$moduleId})!";
    }
} catch (PDOException $e) {
    echo "Ошибка при добавлении модуля: " . $e->getMessage();
}
?> 