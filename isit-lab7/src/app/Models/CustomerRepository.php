<?php
// app/Models/CustomerRepository.php — модель клиентов.

declare(strict_types=1);

namespace App\Models;

final class CustomerRepository extends JsonRepository
{
    protected string $name = 'customers'; // файл storage/customers.json
}