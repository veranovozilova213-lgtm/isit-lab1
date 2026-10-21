<h1>Новый заказ</h1>

<form action="/tasks" method="post" class="card">
  <div class="form-group">
    <label for="title">Номер заказа</label>
    <input id="title" type="text" name="title" value="<?= e($old['title'] ?? '') ?>" placeholder="Например: 4657552">
    <?php if (isset($errors['title'])): ?><div class="error-text"><?= e($errors['title']) ?></div><?php endif; ?>
  </div>

  <div class="form-group">
    <label for="courier">Курьер</label>
    <input id="courier" type="text" name="courier" value="<?= e($old['courier'] ?? '') ?>" placeholder="Имя курьера">
    <?php if (isset($errors['courier'])): ?><div class="error-text"><?= e($errors['courier']) ?></div><?php endif; ?>
  </div>

  <div class="form-group">
    <label for="address">Адрес доставки</label>
    <input id="address" type="text" name="address" value="<?= e($old['address'] ?? '') ?>" placeholder="Город, улица, дом">
    <?php if (isset($errors['address'])): ?><div class="error-text"><?= e($errors['address']) ?></div><?php endif; ?>
  </div>

  <div class="form-group">
    <label for="sum">Сумма, ₽</label>
    <input id="sum" type="number" name="sum" value="<?= e($old['sum'] ?? '') ?>" placeholder="Например: 1200">
  </div>

  <div style="margin-top: 30px;">
    <button type="submit" class="btn">Создать заказ</button>
    <a href="/tasks" style="margin-left: 15px; color: #555; text-decoration: none;">Отмена</a>
  </div>
</form>