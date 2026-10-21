<?php
$statusNames = ['new' => 'Новый', 'in_progress' => 'В пути', 'done' => 'Доставлен'];
?>
<p style="margin-bottom: 20px;"><a href="/tasks" style="color: #555; text-decoration: none;">← Назад к списку</a></p>

<div class="card">
  <h1 style="margin-top: 0; margin-bottom: 30px;"><?= e($task['title'] ?? 'Заказ №' . $task['id']) ?></h1>
  
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 20px;">
    <div>
      <p style="margin-bottom: 10px;"><strong>Номер:</strong> <?= e($task['id']) ?></p>
      <p><strong>Статус:</strong> <?= e($statusNames[$task['status']] ?? $task['status']) ?></p>
    </div>
    <div>
      <p style="margin-bottom: 10px;"><strong>Курьер:</strong> <?= e($task['courier'] ?? '—') ?></p>
      <p><strong>Сумма:</strong> <?= e($task['sum'] ?? '0') ?> ₽</p>
    </div>
  </div>
  
  <div style="margin-top: 30px; font-size: 20px; padding-top: 20px; border-top: 1px solid #b0b0b0;">
    <p><strong>Адрес доставки:</strong> <?= e($task['address'] ?? '—') ?></p>
  </div>
</div>