# Лабораторная работа №8

## Проектирование и реализация базы данных

**Предметная область:** Приложение доставки заказов
**СУБД:** PostgreSQL 18

**Цель работы**
Изучить основы проектирования реляционных баз данных, разработать структуру базы данных PostgreSQL, реализовать связи между сущностями, выполнить основные операции по работе с данными и освоить базовые методы оптимизации SQL-запросов.

### Краткое описание выполненного задания

Для системы «Приложение доставки заказов» (лабораторные работы №1–7) спроектирована и реализована база данных PostgreSQL 18: 7 таблиц, связи «один к одному», «один ко многим» и «многие ко многим», ограничения целостности (PRIMARY KEY, FOREIGN KEY, NOT NULL, UNIQUE, CHECK, DEFAULT). Подготовлены SQL-скрипты создания таблиц, тестовых данных, проверки ссылочной целостности и CRUD-операций. На 100 000 заказов проанализированы планы частых запросов (EXPLAIN ANALYZE); после добавления индексов они ускорились в десятки раз.

### Предметная область

|Что определить|Значение для системы доставки заказов|
|-|-|
|Основная сущность (1:N)|заказ (`orders`)|
|Связанная сущность (1:N)|курьер (`couriers`)|
|Третья сущность (1:N)|клиент (`customers`)|
|Дополнительные сущности|профиль курьера (`courier\_profiles`), история статусов (`order\_history`), тег (`tags`)|
|Связь 1:1|курьер — профиль|
|Связи 1:N|клиент — заказы, курьер — заказы, заказ — история статусов|
|Связь N:M|заказы — теги (через `order\_tags`)|

Таблицы `orders`, `couriers` и `customers` повторяют ресурсы REST API из лабораторной работы №7 (те же поля и исходные данные), поэтому в следующей работе API можно перевести с JSON-файлов на базу, не меняя формат ответов.

**Запуск (использован установленный PostgreSQL 18 и pgAdmin 4):**
Внутри Query Tool в pgAdmin скрипты выполняются по порядку:

```sql
-- Выполнить по очереди:
\\i sql/01\_schema.sql        -- таблицы и ограничения
\\i sql/02\_seed.sql          -- исходные данные
\\i sql/03\_integrity.sql     -- проверка ограничений и связей
\\i sql/04\_crud.sql          -- CRUD-операции
\\i sql/05\_test\_data.sql     -- 100 000 заказов для проверки производительности
\\i sql/06\_optimization.sql  -- EXPLAIN ANALYZE до и после индексов
```

### Структура базы данных

ER-диаграмма



### 1\. Предметная область

|Что определить|Значение для системы доставки заказов|
|-|-|
|Основная сущность (1:N)|заказ (`orders`)|
|Связанная сущность (1:N)|курьер (`couriers`)|
|Третья сущность (1:N)|клиент (`customers`)|
|Дополнительные сущности|профиль курьера (`courier\_profiles`), история статусов (`order\_history`), тег (`tags`)|
|Связь 1:1|курьер — профиль|
|Связи 1:N|клиент — заказы, курьер — заказы, заказ — история статусов|
|Связь N:M|заказы — теги (через `order\_tags`)|

\---

### 2\. Таблица `customers` — клиенты

|Поле|Тип|Ограничения|Описание|
|-|-|-|-|
|id|SERIAL|PRIMARY KEY|номер клиента|
|name|VARCHAR(100)|NOT NULL|имя|
|email|VARCHAR(100)|NOT NULL, UNIQUE|почта, не повторяется|
|phone|VARCHAR(20)|NOT NULL|телефон|

\---

### 3\. Таблица `couriers` — курьеры

|Поле|Тип|Ограничения|Описание|
|-|-|-|-|
|id|SERIAL|PRIMARY KEY|номер курьера|
|name|VARCHAR(100)|NOT NULL|имя|
|phone|VARCHAR(20)|NOT NULL|телефон|

\---

### 4\. Таблица `courier\_profiles` — профиль курьера (связь 1:1)

|Поле|Тип|Ограничения|Описание|
|-|-|-|-|
|courier\_id|INTEGER|PRIMARY KEY, REFERENCES couriers ON DELETE CASCADE|курьер; одновременно первичный и внешний ключ|
|passport\_data|VARCHAR(200)|NOT NULL|паспортные данные|
|transport\_type|VARCHAR(50)|NOT NULL|тип транспорта|

\---

### 5\. Таблица `orders` — заказы (основная сущность)

|Поле|Тип|Ограничения|Описание|
|-|-|-|-|
|id|SERIAL|PRIMARY KEY|номер заказа|
|customer\_id|INTEGER|NOT NULL, REFERENCES customers ON DELETE RESTRICT|клиент|
|courier\_id|INTEGER|REFERENCES couriers ON DELETE SET NULL|курьер, может отсутствовать|
|title|VARCHAR(150)|NOT NULL|номер заказа|
|address|TEXT|NOT NULL|адрес доставки|
|sum|NUMERIC(12,2)|NOT NULL, CHECK (sum > 0)|сумма заказа (как в API)|
|status|VARCHAR(20)|NOT NULL, DEFAULT 'new', CHECK|new, in\_progress, done, cancelled|
|created\_at|TIMESTAMP|NOT NULL, DEFAULT NOW()|дата создания|

\---

### 6\. Таблицы `tags` и `order\_tags` — теги и связь «заказ — тег» (N:M)

|Таблица|Поле|Тип|Ограничения|
|-|-|-|-|
|tags|id|SERIAL|PRIMARY KEY|
|tags|name|VARCHAR(50)|NOT NULL, UNIQUE|
|order\_tags|order\_id|INTEGER|REFERENCES orders ON DELETE CASCADE|
|order\_tags|tag\_id|INTEGER|REFERENCES tags ON DELETE CASCADE|
|order\_tags|||PRIMARY KEY (order\_id, tag\_id) — пара не повторяется|

\---

### 7\. Таблица `order\_history` — история статусов

|Поле|Тип|Ограничения|Описание|
|-|-|-|-|
|id|SERIAL|PRIMARY KEY|номер записи|
|order\_id|INTEGER|NOT NULL, REFERENCES orders ON DELETE CASCADE|заказ|
|status\_name|VARCHAR(50)|NOT NULL|статус|
|comment|TEXT||комментарий|
|changed\_at|TIMESTAMP|NOT NULL, DEFAULT NOW()|дата|

\---

### 8\. Связи между сущностями

|Связь|Тип|Как реализована|При удалении «главной» строки|
|-|-|-|-|
|курьер — профиль|1:1|`courier\_profiles.courier\_id` — первичный и внешний ключ|профиль удаляется (CASCADE)|
|клиент — заказы|1:N|`orders.customer\_id`|запрет, пока у клиента есть заказы (RESTRICT)|
|курьер — заказы|1:N|`orders.courier\_id`, может быть NULL|заказы остаются без курьера (SET NULL)|
|заказ — история|1:N|`order\_history.order\_id`|история удаляется (CASCADE)|
|заказы — теги|N:M|связующая таблица `order\_tags`|строки связи удаляются (CASCADE)|

\---

### 9\. Проверка ссылочной целостности

|№|Что пытаемся сделать|Ответ PostgreSQL|
|-|-|-|
|1|заказ для клиента №99|violates foreign key constraint "orders\_customer\_id\_fkey"|
|2|заказ с суммой -100|violates check constraint "orders\_sum\_check"|
|3|второй профиль для курьера №1|duplicate key value violates unique constraint "courier\_profiles\_pkey"|
|4|удалить клиента №1 с заказами|violates foreign key constraint "orders\_customer\_id\_fkey" (RESTRICT)|

\---

### 10\. Результаты оптимизации запросов

|№|Запрос|Индекс|План до|План после|До, мс|После, мс|Ускорение|
|-|-|-|-|-|-|-|-|
|1|заказы клиента (`WHERE customer\_id = 2`)|`orders (customer\_id)`|Seq Scan|Bitmap Index Scan|15,0|0,50|×30|
|2|доставленные заказы по дате (`WHERE status = 'done' ORDER BY created\_at DESC`)|`orders (status, created\_at DESC)`|Seq Scan + Sort|Index Scan, без сортировки|20,3|1,05|×20|



\---

## Примеры SQL-запросов (CRUD)

Все запросы находятся в файле `sql/04\_crud.sql`. Для каждого указан адрес REST API из лабораторной работы №7, который будет его выполнять в следующей лабораторной.

|Операция|SQL|REST API|
|-|-|-|
|Create|`INSERT INTO orders (...) VALUES (...) RETURNING ...`|POST /api/orders|
|Read (список)|`SELECT ... FROM orders WHERE status = 'new' ORDER BY sum DESC`|GET /api/orders?status=new|
|Read (одна)|`SELECT ... FROM orders WHERE id = 5`|GET /api/orders/5|
|Update|`UPDATE orders SET status = 'in\_progress' WHERE id = 5 RETURNING ...`|PATCH /api/orders/{id}|
|Delete|`DELETE FROM orders WHERE id = 5 RETURNING ...`|DELETE /api/orders/{id}|

**Заказы с клиентом и курьером (LEFT JOIN, потому что у части заказов курьера нет):**

```sql
SELECT o.id, o.title, c.name AS customer, cr.name AS courier, o.status
FROM orders o
JOIN customers c ON c.id = o.customer\_id
LEFT JOIN couriers cr ON cr.id = o.courier\_id
ORDER BY o.id;
```

**Теги заказа одной строкой (связь N:M):**

```sql
SELECT o.id, o.title, string\_agg(t.name, ', ' ORDER BY t.name) AS tags
FROM orders o
JOIN order\_tags ot ON ot.order\_id = o.id
JOIN tags t        ON t.id = ot.tag\_id
GROUP BY o.id, o.title
ORDER BY o.id;
```

**Отчет по клиентам (GROUP BY):**

```sql
SELECT c.name,
       COUNT(o.id) AS total\_orders,
       SUM(o.sum) AS total\_spent
FROM customers c
LEFT JOIN orders o ON o.customer\_id = c.id
GROUP BY c.id, c.name
ORDER BY total\_spent DESC;
```

\---

## Результаты оптимизации запросов

Для проверки скорости скрипт `sql/05\_test\_data.sql` добавляет 100 000 заказов. Затем `sql/06\_optimization.sql` для каждого частого запроса выполняет `EXPLAIN ANALYZE` без индекса, создаёт индекс и повторяет `EXPLAIN ANALYZE`.

|№|Запрос|Индекс|План до|План после|До, мс|После, мс|Ускорение|
|-|-|-|-|-|-|-|-|
|1|заказы клиента (`WHERE customer\_id = 2`)|`orders (customer\_id)`|Seq Scan|Bitmap Index Scan|15,0|0,50|×30|
|2|доставленные заказы по дате (`WHERE status = 'done' ORDER BY created\_at DESC`)|`orders (status, created\_at DESC)`|Seq Scan + Sort|Index Scan, без сортировки|20,3|1,05|×20|

**Наблюдения:**

* PostgreSQL не создаёт индексы для внешних ключей автоматически, поэтому запрос 1 до оптимизации перебирал всю таблицу.
* В запросе 2 составной индекс `(status, created\_at DESC)` хранит строки уже отсортированными, и из плана исчез шаг `Sort`: база читает нужные записи сразу.
* Индекс не помогает, если запрос читает большую часть таблицы или ищет подстроку `LIKE '%...%'`: планировщик всё равно выбирает `Seq Scan`.
* У индексов есть цена: они занимают место на диске и немного замедляют операции `INSERT` и `UPDATE`.

\---

## Вывод

В результате работы спроектирована и реализована реляционная база данных PostgreSQL для системы доставки заказов: 7 таблиц, связи всех трёх типов (1:1, 1:N и N:M) и ограничения целостности.

Правила, которые в лабораторной работе №7 проверял PHP-код (допустимые статусы, сумма больше нуля, существование клиента, запрет удалить клиента с заказами), теперь дополнительно проверяет сама база. Их невозможно обойти из любой программы, что подтверждено скриптом `03\_integrity.sql`.

CRUD-запросы полностью соответствуют методам REST API и будут использованы при подключении базы к серверной части. Анализ планов `EXPLAIN ANALYZE` на 100 000 заказов показал, что индексы на внешние ключи и на столбцы фильтрации и сортировки ускоряют частые запросы в десятки раз.

\---

## Структура репозитория

```text
lab8/
├── README.md
├── sql/
│   ├── 01\_schema.sql         # создание таблиц и ограничений
│   ├── 02\_seed.sql           # исходные данные
│   ├── 03\_integrity.sql      # проверка ограничений и ссылочной целостности
│   ├── 04\_crud.sql           # CRUD-операции
│   ├── 05\_test\_data.sql      # большой объём данных для оптимизации
│   └── 06\_optimization.sql   # EXPLAIN ANALYZE до и после индексов
├── diagrams/
│   ├── images/er.png         # ER-диаграмма
│   └── src/er.puml           # исходник ER-диаграммы
├── images/                   # скриншоты выполнения скриптов
└── docs/
    └── optimization.md       # (опционально) полные планы выполнения
```

