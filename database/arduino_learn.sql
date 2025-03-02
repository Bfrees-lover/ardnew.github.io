-- Создание базы данных
CREATE DATABASE IF NOT EXISTS arduino_learn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Используем созданную базу данных
USE arduino_learn;

-- Создание таблицы для модулей
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    url VARCHAR(255) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Создание таблицы для пользователей (администраторов)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'editor') NOT NULL DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Вставка тестовых данных для модулей
INSERT INTO modules (title, description, url, active, sort_order) VALUES
('Основы Arduino', 'Познакомьтесь с платформой Arduino, узнайте о компонентах и базовых принципах работы.', 'basics.html', 1, 1),
('Цифровые входы и выходы', 'Изучите работу с цифровыми пинами, кнопками и светодиодами.', 'digital-io.html', 1, 2),
('Аналоговые сигналы', 'Научитесь работать с аналоговыми датчиками и ШИМ-сигналами.', 'analog.html', 1, 3),
('Сенсоры и датчики', 'Изучите различные типы датчиков и их применение в проектах.', 'sensors.html', 1, 4),
('Дисплеи и индикация', 'Освойте работу с LCD, LED и другими типами дисплеев.', 'displays.html', 1, 5),
('Моторы и сервоприводы', 'Научитесь управлять различными типами двигателей.', 'motors.html', 1, 6),
('Программирование Arduino', 'Изучите основы программирования Arduino на C++: переменные, функции, циклы и многое другое.', 'programming.html', 1, 7),
('Компоненты Arduino', 'Изучите различные компоненты и модули, совместимые с Arduino: датчики, дисплеи, моторы и многое другое.', 'components.html', 1, 8),
('Шаблоны кода', 'Готовые шаблоны и примеры кода для типовых задач: работа с кнопками, датчиками, дисплеями и другими компонентами.', 'code-templates.html', 1, 9);

-- Вставка администратора (пароль: admin123)
-- Пароль хешируется с использованием PASSWORD_DEFAULT алгоритма PHP
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$8KzO3LOgpJ6L.U9N69URt.Kh5yyqIxJh0/u3TS9KFOJhnNpi9JKnK', 'admin@example.com', 'admin'); 