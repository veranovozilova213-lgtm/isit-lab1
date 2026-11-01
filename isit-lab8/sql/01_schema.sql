-- 01_schema.sql — структура базы данных для приложения доставки

-- Удаляем старые таблицы (если они есть), чтобы можно было запускать скрипт много раз
DROP TABLE IF EXISTS order_history CASCADE;
DROP TABLE IF EXISTS order_tags CASCADE;
DROP TABLE IF EXISTS tags CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS courier_profiles CASCADE;
DROP TABLE IF EXISTS couriers CASCADE;
DROP TABLE IF EXISTS customers CASCADE;

-- 1. Клиенты (основная третья сущность)
CREATE TABLE customers (
    id      SERIAL PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    email   VARCHAR(100) UNIQUE NOT NULL,
    phone   VARCHAR(20) NOT NULL
);

-- 2. Курьеры (связанная сущность 1:N)
CREATE TABLE couriers (
    id      SERIAL PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    phone   VARCHAR(20) NOT NULL
);

-- 3. Профили курьеров (связь 1:1)
CREATE TABLE courier_profiles (
    courier_id      INTEGER PRIMARY KEY REFERENCES couriers(id) ON DELETE CASCADE,
    passport_data   VARCHAR(200) NOT NULL,
    transport_type  VARCHAR(50) NOT NULL
);

-- 4. Заказы (ОСНОВНАЯ сущность)
CREATE TABLE orders (
    id          SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE RESTRICT,
    courier_id  INTEGER REFERENCES couriers(id) ON DELETE SET NULL,
    title       VARCHAR(150) NOT NULL,
    address     TEXT NOT NULL,
    sum         NUMERIC(12, 2) NOT NULL CHECK (sum > 0),
    status      VARCHAR(20) NOT NULL DEFAULT 'new' CHECK (status IN ('new', 'in_progress', 'done', 'cancelled')),
    created_at  TIMESTAMP NOT NULL DEFAULT NOW()
);

-- 5. Категории/Теги
CREATE TABLE tags (
    id      SERIAL PRIMARY KEY,
    name    VARCHAR(50) NOT NULL UNIQUE
);

-- 6. Связь N:M (Заказы и Теги)
CREATE TABLE order_tags (
    order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    tag_id   INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (order_id, tag_id)
);

-- 7. История статусов / комментарии к заказу (Дополнительная сущность 1:N)
CREATE TABLE order_history (
    id          SERIAL PRIMARY KEY,
    order_id    INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    status_name VARCHAR(50) NOT NULL,
    comment     TEXT,
    changed_at  TIMESTAMP NOT NULL DEFAULT NOW()
);