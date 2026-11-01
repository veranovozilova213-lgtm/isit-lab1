-- 1. Клиенты
INSERT INTO customers (name, email, phone) VALUES
('Вася Пупкин', 'vasya@mail.ru', '+7 (999) 111-22-33'),
('Анна Смирнова', 'anna@mail.ru', '+7 (999) 444-55-66'),
('Иван Петров', 'ivan@mail.ru', '+7 (999) 777-88-99');

-- 2. Курьеры
INSERT INTO couriers (name, phone) VALUES
('Ашот', '+7 (900) 111-11-11'),
('Иван', '+7 (900) 222-22-22'),
('Пётр', '+7 (900) 333-33-33');

-- 3. Профили курьеров
INSERT INTO courier_profiles (courier_id, passport_data, transport_type) VALUES
(1, 'Серия 4501 № 123456', 'Личный автомобиль'),
(2, 'Серия 4502 № 654321', 'Пешком'),
(3, 'Серия 4503 № 112233', 'Велосипед');

-- 4. Заказы
INSERT INTO orders (customer_id, courier_id, title, address, sum, status) VALUES
(1, 1, 'Заказ № 4657545', 'г. Зеленоград, ул. Юности, 9', 1200.00, 'in_progress'),
(2, 2, 'Заказ № 4657546', 'г. Москва, ул. Ленина, 1', 800.00, 'new'),
(3, 1, 'Заказ № 4657547', 'г. Москва, ул. Тверская, 7', 1500.00, 'done'),
(1, NULL, 'Заказ № 4657548', 'г. Химки, ул. Мира, 15', 450.00, 'new'), 
(2, 3, 'Заказ № 4657549', 'г. Москва, пр. Мира, 100', 2100.00, 'cancelled');

-- 5. Теги
INSERT INTO tags (name) VALUES
('Хрупкий груз'),
('Срочная доставка'),
('Документы');

-- 6. Связь Заказов и Тегов
INSERT INTO order_tags (order_id, tag_id) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 2);

-- 7. История статусов
INSERT INTO order_history (order_id, status_name, comment) VALUES
(1, 'new', 'Заказ создан'),
(1, 'in_progress', 'Курьер выехал'),
(3, 'done', 'Доставлено лично в руки');