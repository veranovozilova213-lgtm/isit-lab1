<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Страница') ?> — Доставка</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <header class="topbar">
    <div class="nav-container">
      <a href="/" class="nav-link <?= ($_SERVER['REQUEST_URI'] ?? '') === '/' ? 'active' : '' ?>">Сводка</a>
      <a href="/tasks" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/tasks') ? 'active' : '' ?>">Список заказов</a>
      <a href="/projects" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/projects') ? 'active' : '' ?>">Курьеры</a>
      <a href="/tasks/create" class="btn">+ Новый заказ</a>
    </div>
  </header>
  <main class="container">