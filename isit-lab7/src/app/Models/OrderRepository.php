<?php
// app/Models/OrderRepository.php — модель заказов.

declare(strict_types=1);

namespace App\Models;

final class OrderRepository extends JsonRepository
{
    protected string $name = 'orders';  // файл storage/orders.json
}