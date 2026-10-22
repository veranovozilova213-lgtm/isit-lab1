<?php
// routes/web.php — таблица маршрутов приложения: «какой адрес — какой контроллер и метод».
// Переменная $router создаётся в public/index.php.

use App\Controllers\Api\CourierApiController;
use App\Controllers\Api\OrderApiController;
use App\Controllers\Api\CustomerApiController;
use App\Controllers\HomeController;
use App\Controllers\CourierController;
use App\Controllers\OrderController;

$router->get('/', [HomeController::class, 'index']);

// --- МАРШРУТЫ САЙТА (HTML) ---
// Заказы (вместо задач)
$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/create', [OrderController::class, 'create']);   // ВАЖНО: раньше, чем /orders/{id}
$router->post('/orders', [OrderController::class, 'store']);
$router->get('/orders/{id}', [OrderController::class, 'show']);

// Курьеры (вместо проектов)
$router->get('/couriers', [CourierController::class, 'index']);
$router->get('/couriers/{id}', [CourierController::class, 'show']);

// ---------- REST API (все адреса начинаются с /api, ответы в формате JSON) ----------
// Маршруты клиентов готовы: используйте их как образец.
// TODO 1.1  Зарегистрируйте шесть маршрутов для заказов по образцу клиентов (замените customers на orders,
//           CustomerApiController на OrderApiController):
//             GET    /api/orders          index
//             POST   /api/orders          store
//             GET    /api/orders/{id}     show
//             PUT    /api/orders/{id}     update
//             PATCH  /api/orders/{id}     update      (тот же метод, что и у PUT)
//             DELETE /api/orders/{id}     destroy
//           ✔ Проверка: запрос GET /api/orders вернёт JSON с заказами, а не ошибку 404.

$router->get('/api/orders', [OrderApiController::class, 'index']);
$router->post('/api/orders', [OrderApiController::class, 'store']);
$router->get('/api/orders/{id}', [OrderApiController::class, 'show']);
$router->put('/api/orders/{id}', [OrderApiController::class, 'update']);
$router->patch('/api/orders/{id}', [OrderApiController::class, 'update']);
$router->delete('/api/orders/{id}', [OrderApiController::class, 'destroy']);

// TODO 1.2  То же для курьеров (CourierApiController, адрес /api/couriers) + ещё один, седьмой маршрут:
//             GET    /api/couriers/{id}/orders    orders      (заказы курьера; метод orders() уже написан)
//           ✔ Проверка: GET /api/couriers/1/orders возвращает список заказов этого курьера.

$router->get('/api/couriers', [CourierApiController::class, 'index']);
$router->post('/api/couriers', [CourierApiController::class, 'store']);
$router->get('/api/couriers/{id}', [CourierApiController::class, 'show']);
$router->put('/api/couriers/{id}', [CourierApiController::class, 'update']);
$router->patch('/api/couriers/{id}', [CourierApiController::class, 'update']);
$router->delete('/api/couriers/{id}', [CourierApiController::class, 'destroy']);

// Вложенный маршрут (заказы курьера)
$router->get('/api/couriers/{id}/orders', [CourierApiController::class, 'orders']);

// Маршруты клиентов (готовы изначально, переименованы из пользователей)
$router->get('/api/customers', [CustomerApiController::class, 'index']);
$router->post('/api/customers', [CustomerApiController::class, 'store']);
$router->get('/api/customers/{id}', [CustomerApiController::class, 'show']);
$router->put('/api/customers/{id}', [CustomerApiController::class, 'update']);
$router->patch('/api/customers/{id}', [CustomerApiController::class, 'update']);
$router->delete('/api/customers/{id}', [CustomerApiController::class, 'destroy']);