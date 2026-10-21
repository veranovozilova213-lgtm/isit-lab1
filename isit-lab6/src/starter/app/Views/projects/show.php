<?php
// Шаблон просмотра одного курьера.
?>
<p><a href="/projects" style="color: #555; text-decoration: none;">← К списку курьеров</a></p>

<div class="card">
  <h1 style="margin-top: 0;">Курьер: <?= e($project['title']) ?></h1>
  
  <p style="font-size: 20px; color: #333; margin-bottom: 30px;">
    <?= e($project['description'] ?? 'Описание отсутствует') ?>
  </p>
  
  <div>
    <a href="/tasks" class="btn">Перейти к заказам</a>
  </div>
</div>