<?php
// app/Controllers/HomeController.php — контроллер главной страницы.

declare(strict_types=1);

namespace App\Controllers;

use App\Models\OrderRepository;
use App\Models\CourierRepository;

final class HomeController
{
    public function index(): void
    {
        $orders = (new OrderRepository())->all();
        $couriers = (new CourierRepository())->all();

        // Считаем заказы по статусам
        $byStatus = ['new' => 0, 'in_progress' => 0, 'done' => 0];
        foreach ($orders as $order) {
            if (isset($byStatus[$order['status']])) {
                $byStatus[$order['status']]++;
            }
        }

        view('home', [
            'title'         => 'Сводка',
            'total'         => count($orders),
            'byStatus'      => $byStatus,
            'projectsCount' => count($couriers), // шаблон ждет это имя для курьеров
        ]);
    }
}