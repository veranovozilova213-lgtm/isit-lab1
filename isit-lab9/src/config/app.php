<?php
// config/app.php — настройки приложения. Файл возвращает массив.

return [
    'name'         => 'Доставка заказов', // название системы
    'debug'        => true,               // true — в ответе 500 показывается текст ошибки (только при разработке!)
    'token_ttl'    => 7200,               // срок жизни токена входа, секунд (2 часа)
    'cors_origins' => [],                 // адреса клиентов с ДРУГОГО origin, которым разрешён доступ к API
                                          // (пример: ['http://localhost:5500']); для клиента с того же адреса не нужно

    // Подключение к PostgreSQL. 
    'db' => [
        'host'     => getenv('DB_HOST') ?: '127.0.0.1',
        'port'     => (int) (getenv('DB_PORT') ?: 5432),
        'name'     => getenv('DB_NAME') ?: 'taskapp',
        'user'     => getenv('DB_USER') ?: 'postgres',
        'password' => getenv('DB_PASSWORD') ?: 'tresandra213', 
    ],
];