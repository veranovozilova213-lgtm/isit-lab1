<h1>Сводка по задачам</h1>

<div class="grid">
  <div class="stat-card">
    <h3>Всего заказов</h3>
    <div class="number"><?= $total ?></div>
  </div>

  <div class="stat-card">
    <h3>Новые</h3>
    <div class="number"><?= $byStatus['new'] ?></div>
  </div>

  <div class="stat-card">
    <h3>В пути</h3>
    <div class="number"><?= $byStatus['in_progress'] ?></div>
  </div>

  <div class="stat-card">
    <h3>Доставлено</h3>
    <div class="number"><?= $byStatus['done'] ?></div>
  </div>

  <div class="stat-card">
    <h3>Курьеры</h3>
    <div class="number"><?= $projectsCount ?></div>
  </div>
</div>

<div style="margin-top: 30px;">
  <a href="/tasks" class="btn">Перейти к заказам</a>
  <a href="/projects" class="btn" style="margin-left: 10px;">Список курьеров</a>
</div>