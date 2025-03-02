<?php
// Начинаем сессию
session_start();

// Подключаем файл с настройками базы данных
require_once '../config/database.php';

// Устанавливаем заголовок для JSON
header('Content-Type: application/json');

// Включаем отладочную информацию
$debug = true;
$debug_info = [];

// Проверяем, что запрос отправлен методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $debug_info['username'] = $username;
    $debug_info['password_length'] = strlen($password);
    
    // Проверяем, что поля не пустые
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Введите имя пользователя и пароль']);
        exit;
    }
    
    try {
        // Ищем пользователя в базе данных
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? AND role = 'admin' LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $debug_info['user_found'] = !empty($user);
        if ($user) {
            $debug_info['stored_hash'] = $user['password'];
            $debug_info['password_verify_result'] = password_verify($password, $user['password']);
        }
        
        // Проверяем, найден ли пользователь и верен ли пароль
        if ($user && password_verify($password, $user['password'])) {
            // Сохраняем информацию о пользователе в сессии
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = true;
            
            // Возвращаем успешный ответ
            echo json_encode(['success' => true]);
        } else {
            // Возвращаем сообщение об ошибке
            echo json_encode(['success' => false, 'message' => 'Неверное имя пользователя или пароль', 'debug' => $debug ? $debug_info : null]);
        }
    } catch (PDOException $e) {
        // В случае ошибки возвращаем сообщение об ошибке
        echo json_encode(['success' => false, 'message' => 'Ошибка при входе: ' . $e->getMessage(), 'debug' => $debug ? $debug_info : null]);
    }
} else {
    // Если запрос отправлен не методом POST, возвращаем ошибку
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
}
?> 