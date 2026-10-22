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
      <a href="/" class="nav-link">Сводка</a>
      <a href="/orders" class="nav-link">Список заказов</a>
      <a href="/couriers" class="nav-link">Курьеры</a>
      <a href="/orders/create" class="btn">+ Новый заказ</a>
    </div>
  </header>
  <main class="container">