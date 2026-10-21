<?php
// routes/web.php — таблица маршрутов приложения.

use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Controllers\TaskController;

// Главная страница
$router->get('/', [HomeController::class, 'index']);

// Маршруты для задач
$router->get('/tasks', [TaskController::class, 'index']);
$router->get('/tasks/create', [TaskController::class, 'create']);
$router->post('/tasks', [TaskController::class, 'store']);
$router->get('/tasks/{id}', [TaskController::class, 'show']);

// Маршруты для проектов
$router->get('/projects', [ProjectController::class, 'index']);
$router->get('/projects/{id}', [ProjectController::class, 'show']);