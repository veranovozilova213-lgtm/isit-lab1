<?php
$statusNames = ['new' => 'Новый', 'in_progress' => 'В пути', 'done' => 'Доставлен'];
?>
<h1>Список заказов</h1>

<div class="filters">
  <a href="/tasks" class="filter-btn <?= $status === '' ? 'active' : '' ?>">Все</a>
  <?php foreach ($statusNames as $code => $name): ?>
    <a href="/tasks?status=<?= e($code) ?>" class="filter-btn <?= $status === $code ? 'active' : '' ?>"><?= e($name) ?></a>
  <?php endforeach; ?>
</div>

<?php if (count($tasks) === 0): ?>
  <div class="card">Заказы не найдены.</div>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>№</th>
        <th>Заказ</th>
        <th>Статус</th>
        <th>Курьер</th>
        <th>Адрес</th>
        <th>Сумма</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tasks as $task): ?>
        <tr>
          <td><?= e($task['id']) ?></td>
          <td><a href="/tasks/<?= e($task['id']) ?>"><?= e($task['title']) ?></a></td>
          <td><?= e($statusNames[$task['status']] ?? $task['status']) ?></td>
          <td><?= e($task['courier'] ?? '—') ?></td>
          <td><?= e($task['address'] ?? '—') ?></td>
          <td><?= e($task['sum'] ?? '0') ?> ₽</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>