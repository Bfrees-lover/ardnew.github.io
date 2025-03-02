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

// Проверяем, передан ли ID модуля
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Если нет, перенаправляем на панель управления
    header('Location: dashboard.php');
    exit;
}

$module_id = intval($_GET['id']);

// Получаем информацию о модуле из базы данных
try {
    $stmt = $pdo->prepare("SELECT id, title, url FROM modules WHERE id = ?");
    $stmt->execute([$module_id]);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$module) {
        // Если модуль не найден, перенаправляем на панель управления
        header('Location: dashboard.php');
        exit;
    }
} catch (PDOException $e) {
    $error = 'Ошибка при получении информации о модуле: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование содержимого модуля</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Подключаем CodeMirror для редактирования HTML -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
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
        
        .editor-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .CodeMirror {
            height: 70vh;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        
        .editor-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status-message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
        }
        
        .status-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .status-error {
            background-color: var(--danger-color);
            color: white;
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
        
        .loading {
            display: none;
            align-items: center;
            gap: 10px;
        }
        
        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Редактирование содержимого модуля: <?php echo htmlspecialchars($module['title']); ?></h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="window.location.href='dashboard.php'">Вернуться к панели</button>
            </div>
        </header>
        
        <?php if (isset($error)): ?>
            <div class="panel status-message status-error" style="display: block;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div id="status-message" class="status-message"></div>
        
        <div class="panel">
            <div class="editor-container">
                <div class="editor-actions">
                    <div>
                        <button class="btn btn-primary" onclick="previewContent()">Предпросмотр</button>
                    </div>
                    <div class="loading" id="loading">
                        <div class="loading-spinner"></div>
                        <span>Загрузка...</span>
                    </div>
                    <div>
                        <button class="btn btn-success" onclick="saveContent()">Сохранить</button>
                    </div>
                </div>
                
                <textarea id="editor"></textarea>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для предпросмотра -->
    <div id="preview-modal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="modal-content" style="background-color: var(--panel-bg); border-radius: 8px; padding: 20px; width: 80%; height: 80%; max-width: 1000px; overflow: auto;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Предпросмотр</h2>
                <span class="close" onclick="closePreviewModal()" style="font-size: 24px; cursor: pointer;">&times;</span>
            </div>
            <iframe id="preview-iframe" style="width: 100%; height: calc(100% - 60px); border: 1px solid var(--border-color); border-radius: 4px;"></iframe>
            <div id="preview-content" style="display: none;"></div>
        </div>
    </div>
    
    <!-- Кнопка переключения темы -->
    <button id="theme-toggle" onclick="toggleTheme()">🌙</button>
    
    <script>
        let editor;
        let moduleUrl = '<?php echo htmlspecialchars($module['url']); ?>';
        
        // Инициализация редактора CodeMirror
        document.addEventListener('DOMContentLoaded', function() {
            // Создаем экземпляр редактора
            editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
                mode: 'htmlmixed',
                theme: localStorage.getItem('theme') === 'dark' ? 'monokai' : 'default',
                lineNumbers: true,
                lineWrapping: true,
                indentUnit: 4,
                autoCloseTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                matchTags: {bothTags: true},
                extraKeys: {"Ctrl-Space": "autocomplete"}
            });
            
            // Загружаем содержимое модуля
            loadModuleContent();
            
            // Устанавливаем начальную тему
            setInitialTheme();
        });
        
        // Функция для загрузки содержимого модуля
        function loadModuleContent() {
            showLoading(true);
            
            fetch('api/get_module_content.php?url=' + encodeURIComponent(moduleUrl), {
                headers: {
                    'Accept': 'application/json; charset=UTF-8',
                    'Cache-Control': 'no-cache'
                }
            })
                .then(response => {
                    // Проверяем заголовок Content-Type
                    const contentType = response.headers.get('Content-Type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }
                    throw new Error('Неверный формат ответа от сервера');
                })
                .then(data => {
                    if (data.success) {
                        // Проверяем наличие проблем с кодировкой
                        let content = data.content;
                        
                        // Проверяем на наличие "битых" символов кириллицы
                        if (/Р|С|Рѕ|РЅ|РІ|Р°/.test(content)) {
                            console.warn('Обнаружены проблемы с кодировкой, пытаемся исправить...');
                            // Пытаемся исправить кодировку на стороне клиента
                            try {
                                // Пробуем декодировать как Windows-1251
                                const decoder = new TextDecoder('windows-1251');
                                const encoder = new TextEncoder();
                                
                                // Преобразуем строку в массив байтов
                                const bytes = new Uint8Array(content.length);
                                for (let i = 0; i < content.length; i++) {
                                    bytes[i] = content.charCodeAt(i) & 0xff;
                                }
                                
                                // Декодируем как Windows-1251 и затем кодируем как UTF-8
                                content = decoder.decode(bytes);
                            } catch (e) {
                                console.error('Ошибка при исправлении кодировки:', e);
                            }
                        }
                        
                        // Устанавливаем содержимое в редактор
                        editor.setValue(content);
                        
                        // Фокусируемся на редакторе
                        editor.focus();
                    } else {
                        showStatusMessage('Ошибка при загрузке содержимого: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    showStatusMessage('Произошла ошибка при загрузке содержимого', 'error');
                })
                .finally(() => {
                    showLoading(false);
                });
        }
        
        // Функция для сохранения содержимого модуля
        function saveContent() {
            showLoading(true);
            
            // Получаем содержимое из редактора
            let content = editor.getValue();
            
            // Проверяем наличие мета-тега с кодировкой UTF-8
            if (!content.includes('<meta charset="UTF-8">') && !content.includes('<meta charset="utf-8">')) {
                // Если мета-тег отсутствует, добавляем его
                if (content.includes('<head>')) {
                    content = content.replace('<head>', '<head>\n  <meta charset="UTF-8">');
                } else if (content.includes('<html>')) {
                    content = content.replace('<html>', '<html>\n<head>\n  <meta charset="UTF-8">\n</head>');
                } else {
                    // Если нет ни head, ни html, добавляем базовую структуру
                    content = '<!DOCTYPE html>\n<html>\n<head>\n  <meta charset="UTF-8">\n</head>\n<body>\n' + content + '\n</body>\n</html>';
                }
            }
            
            // Отправляем запрос на сохранение
            fetch('api/save_module_content.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json; charset=UTF-8',
                },
                body: JSON.stringify({
                    url: moduleUrl,
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatusMessage('Содержимое успешно сохранено', 'success');
                } else {
                    showStatusMessage('Ошибка при сохранении: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showStatusMessage('Произошла ошибка при сохранении содержимого', 'error');
            })
            .finally(() => {
                showLoading(false);
            });
        }
        
        // Функция для предпросмотра содержимого
        function previewContent() {
            // Получаем содержимое из редактора
            const content = editor.getValue();
            
            // Проверяем наличие проблем с кодировкой в предпросмотре
            let previewContent = content;
            
            // Проверяем, есть ли в предпросмотре мета-тег с кодировкой UTF-8
            if (!previewContent.includes('<meta charset="UTF-8">') && !previewContent.includes('<meta charset="utf-8">')) {
                // Если мета-тег отсутствует, добавляем его для предпросмотра
                if (previewContent.includes('<head>')) {
                    previewContent = previewContent.replace('<head>', '<head>\n  <meta charset="UTF-8">');
                } else if (previewContent.includes('<html>')) {
                    previewContent = previewContent.replace('<html>', '<html>\n<head>\n  <meta charset="UTF-8">\n</head>');
                } else if (!previewContent.includes('<html')) {
                    // Если нет HTML-структуры, оборачиваем содержимое в базовую HTML-структуру
                    previewContent = `<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Предпросмотр</title>
  <style>
    body { font-family: Arial, sans-serif; }
  </style>
</head>
<body>
${previewContent}
</body>
</html>`;
                }
            }
            
            // Сохраняем содержимое в скрытом div
            document.getElementById('preview-content').innerHTML = previewContent;
            
            // Используем iframe для предпросмотра с правильной кодировкой
            const iframe = document.getElementById('preview-iframe');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            // Очищаем iframe
            iframeDoc.open();
            
            // Записываем содержимое с явным указанием кодировки
            iframeDoc.write(previewContent);
            iframeDoc.close();
            
            // Отображаем модальное окно
            document.getElementById('preview-modal').style.display = 'flex';
        }
        
        // Функция для закрытия модального окна предпросмотра
        function closePreviewModal() {
            document.getElementById('preview-modal').style.display = 'none';
        }
        
        // Функция для отображения сообщения о статусе
        function showStatusMessage(message, type) {
            const statusElement = document.getElementById('status-message');
            statusElement.textContent = message;
            statusElement.className = 'status-message status-' + type;
            statusElement.style.display = 'block';
            
            // Скрываем сообщение через 5 секунд
            setTimeout(() => {
                statusElement.style.display = 'none';
            }, 5000);
        }
        
        // Функция для отображения/скрытия индикатора загрузки
        function showLoading(show) {
            document.getElementById('loading').style.display = show ? 'flex' : 'none';
        }
        
        // Функция для переключения темы
        function toggleTheme() {
            const body = document.body;
            const themeBtn = document.getElementById('theme-toggle');
            
            if (body.classList.contains('dark-theme')) {
                body.classList.remove('dark-theme');
                themeBtn.textContent = '🌙';
                localStorage.setItem('theme', 'light');
                editor.setOption('theme', 'default');
            } else {
                body.classList.add('dark-theme');
                themeBtn.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
                editor.setOption('theme', 'monokai');
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
    </script>
</body>
</html> 