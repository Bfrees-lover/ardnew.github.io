<?php
// Начинаем сессию
session_start();

// Проверяем, авторизован ли пользователь как администратор
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Если нет, перенаправляем на страницу входа
    header('Location: login.html');
    exit;
}

// Подключаем файл с настройками базы данных
require_once '../config/database.php';

// Получаем список всех модулей
try {
    $stmt = $pdo->query("SELECT id, title, description, url, active, sort_order FROM modules ORDER BY sort_order");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Ошибка при получении модулей: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f5f5f5;
            --panel-bg: #ffffff;
            --text-color: #333333;
            --primary-color: #4285f4;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --border-color: #e0e0e0;
        }
        
        body.dark-theme {
            --bg-color: #121212;
            --panel-bg: #1e1e1e;
            --text-color: #e0e0e0;
            --primary-color: #90caf9;
            --success-color: #81c784;
            --warning-color: #ffb74d;
            --danger-color: #e57373;
            --border-color: #444444;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        h1 {
            margin: 0;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: opacity 0.3s;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .panel {
            background-color: var(--panel-bg);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        th {
            font-weight: 500;
        }
        
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .status-active {
            background-color: var(--success-color);
            color: white;
        }
        
        .status-inactive {
            background-color: var(--danger-color);
            color: white;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: var(--panel-bg);
            border-radius: 8px;
            padding: 20px;
            width: 500px;
            max-width: 90%;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            margin: 0;
        }
        
        .close {
            font-size: 24px;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background-color: var(--bg-color);
            color: var(--text-color);
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        #theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 20px;
            z-index: 900;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Панель администратора</h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openAddModuleModal()">Добавить модуль</button>
                <button class="btn btn-danger" onclick="logout()">Выйти</button>
            </div>
        </header>
        
        <?php if (isset($error)): ?>
            <div class="panel" style="background-color: var(--danger-color); color: white;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="panel">
            <h2>Управление модулями</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>URL</th>
                        <th>Статус</th>
                        <th>Порядок</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($modules) && !empty($modules)): ?>
                        <?php foreach ($modules as $module): ?>
                            <tr>
                                <td><?php echo $module['id']; ?></td>
                                <td><?php echo htmlspecialchars($module['title']); ?></td>
                                <td><?php echo htmlspecialchars(substr($module['description'], 0, 50)) . (strlen($module['description']) > 50 ? '...' : ''); ?></td>
                                <td><?php echo htmlspecialchars($module['url']); ?></td>
                                <td>
                                    <span class="status <?php echo $module['active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $module['active'] ? 'Активен' : 'Неактивен'; ?>
                                    </span>
                                </td>
                                <td><?php echo $module['sort_order']; ?></td>
                                <td class="actions">
                                    <button class="btn btn-warning" onclick="openEditModuleModal(<?php echo htmlspecialchars(json_encode($module)); ?>)">Изменить</button>
                                    <button class="btn btn-primary" onclick="editModuleContent(<?php echo $module['id']; ?>)">Редактировать содержимое</button>
                                    <button class="btn btn-danger" onclick="deleteModule(<?php echo $module['id']; ?>)">Удалить</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Модули не найдены</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Модальное окно для добавления/редактирования модуля -->
    <div id="module-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Добавить модуль</h2>
                <span class="close" onclick="closeModuleModal()">&times;</span>
            </div>
            
            <form id="module-form">
                <input type="hidden" id="module-id" name="id" value="">
                
                <div class="form-group">
                    <label for="title">Название</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="url">URL</label>
                    <input type="text" id="url" name="url" required>
                </div>
                
                <div class="form-group">
                    <label for="active">Статус</label>
                    <select id="active" name="active">
                        <option value="1">Активен</option>
                        <option value="0">Неактивен</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="sort_order">Порядок сортировки</label>
                    <input type="number" id="sort_order" name="sort_order" min="1" value="1">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="closeModuleModal()">Отмена</button>
                    <button type="submit" class="btn btn-success">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Кнопка переключения темы -->
    <button id="theme-toggle" onclick="toggleTheme()">🌙</button>
    
    <script>
        // Функция для переключения темы
        function toggleTheme() {
            const body = document.body;
            const themeBtn = document.getElementById('theme-toggle');
            
            if (body.classList.contains('dark-theme')) {
                body.classList.remove('dark-theme');
                themeBtn.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            } else {
                body.classList.add('dark-theme');
                themeBtn.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // Функция для установки начальной темы
        function setInitialTheme() {
            const savedTheme = localStorage.getItem('theme');
            const themeBtn = document.getElementById('theme-toggle');
            
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
                themeBtn.textContent = '☀️';
            }
        }
        
        // Функция для открытия модального окна добавления модуля
        function openAddModuleModal() {
            // Очищаем форму
            document.getElementById('module-form').reset();
            document.getElementById('module-id').value = '';
            document.getElementById('modal-title').textContent = 'Добавить модуль';
            
            // Открываем модальное окно
            document.getElementById('module-modal').style.display = 'flex';
        }
        
        // Функция для открытия модального окна редактирования модуля
        function openEditModuleModal(module) {
            // Заполняем форму данными модуля
            document.getElementById('module-id').value = module.id;
            document.getElementById('title').value = module.title;
            document.getElementById('description').value = module.description;
            document.getElementById('url').value = module.url;
            document.getElementById('active').value = module.active ? '1' : '0';
            document.getElementById('sort_order').value = module.sort_order;
            
            // Меняем заголовок модального окна
            document.getElementById('modal-title').textContent = 'Редактировать модуль';
            
            // Открываем модальное окно
            document.getElementById('module-modal').style.display = 'flex';
        }
        
        // Функция для закрытия модального окна
        function closeModuleModal() {
            document.getElementById('module-modal').style.display = 'none';
        }
        
        // Функция для удаления модуля
        function deleteModule(id) {
            if (confirm('Вы уверены, что хотите удалить этот модуль?')) {
                // Отправляем запрос на удаление модуля
                fetch('api/delete_module.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Перезагружаем страницу для обновления списка модулей
                        window.location.reload();
                    } else {
                        alert('Ошибка при удалении модуля: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при удалении модуля');
                });
            }
        }
        
        // Функция для выхода из системы
        function logout() {
            // Отправляем запрос на выход
            fetch('api/logout.php')
                .then(() => {
                    // Перенаправляем на страницу входа
                    window.location.href = 'login.html';
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при выходе из системы');
                });
        }
        
        // Функция для редактирования содержимого модуля
        function editModuleContent(id) {
            window.location.href = 'edit_module.php?id=' + id;
        }
        
        // Обработка отправки формы модуля
        document.getElementById('module-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Собираем данные формы
            const formData = new FormData(this);
            const moduleData = {};
            
            for (const [key, value] of formData.entries()) {
                moduleData[key] = value;
            }
            
            // Определяем URL для запроса в зависимости от того, добавляем или редактируем модуль
            const url = moduleData.id ? 'api/update_module.php' : 'api/add_module.php';
            
            // Отправляем запрос
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(moduleData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Закрываем модальное окно и перезагружаем страницу
                    closeModuleModal();
                    window.location.reload();
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при сохранении модуля');
            });
        });
        
        // Вызываем функции при загрузке страницы
        document.addEventListener('DOMContentLoaded', () => {
            setInitialTheme();
        });
    </script>
</body>
</html> 