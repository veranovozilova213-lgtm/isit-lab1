<h1>Курьеры</h1>

<div class="grid">
  <?php foreach ($projects as $project): ?>
    <div class="card" style="margin-bottom: 0;">
      <h2 style="margin-top: 0;"><?= e($project['title']) ?></h2>
      <p style="color: #555; margin-bottom: 20px;"><?= e($project['description'] ?? '') ?></p>
      <a href="/projects/<?= e($project['id']) ?>" class="btn" style="padding: 8px 16px; font-size: 16px;">Подробнее</a>
    </div>
  <?php endforeach; ?>
</div>