Структура проекта

project/
├── frontend/              # Клиентская часть (SPA)
│   ├── pages/             # Экраны: оператор, курьер, клиент
│   └── components/        # Карта, таблица заказов, статус-бар
├── backend/
│   ├── controllers/       # Обработка HTTP-запросов
│   ├── services/          # Логика: назначение курьера, смена статуса
│   ├── repositories/      # Доступ к данным
│   ├── models/            # Order, Courier, Client, Address
│   ├── routes/            # Маршруты API
│   └── notifications/     # Модуль Email/SMS/Push-уведомлений
├── database/
│   ├── migrations/        # Миграции схемы
│   └── seeds/             # Тестовые данные (статусы, роли)
└── docs/                  # Документация и схемы
