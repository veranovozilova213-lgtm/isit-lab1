<?php
// app/Models/CourierRepository.php — модель курьеров.

declare(strict_types=1);

namespace App\Models;

final class CourierRepository extends JsonRepository
{
    protected string $name = 'couriers'; // файл storage/couriers.json
}