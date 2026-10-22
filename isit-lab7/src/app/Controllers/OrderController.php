<?php
// app/Controllers/OrderController.php — контроллер для HTML-страниц сайта.

declare(strict_types=1);

namespace App\Controllers;

use App\Models\OrderRepository;

final class OrderController
{
    private OrderRepository $orders;

    public function __construct()
    {
        $this->orders = new OrderRepository();
    }

    // GET /orders
    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $orders = $this->orders->all();
        
        if ($status !== '') {
            $orders = array_values(array_filter($orders, fn(array $t): bool => $t['status'] === $status));
        }
        
        // Отдаем данные в шаблон из 6-й лабы
        view('tasks/index', [
            'title' => 'Список заказов', 
            'tasks' => $orders, 
            'status' => $status
        ]);
    }

    // GET /orders/5
    public function show(string $id): void
    {
        $order = $this->orders->find((int) $id);
        
        if ($order === null) {
            notFound();
        }
        
        view('tasks/show', [
            'title' => $order['title'],
            'task'  => $order
        ]);
    }

    // GET /orders/create
    public function create(): void
    {
        view('tasks/create', [
            'title'    => 'Новый заказ',
            'errors'   => [],
            'old'      => [],
        ]);
    }

    // POST /orders
    public function store(): void
    {
        $title = trim($_POST['title'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $sum = (int) ($_POST['sum'] ?? 0);

        $errors = [];
        if (mb_strlen($title) < 3) {
            $errors['title'] = 'Номер заказа — не короче 3 символов';
        }
        if ($address === '') {
            $errors['address'] = 'Укажите адрес доставки';
        }

        if ($errors) {
            http_response_code(422);
            view('tasks/create', [
                'title'    => 'Новый заказ',
                'errors'   => $errors,
                'old'      => $_POST,
            ]);
            return;
        }

        $this->orders->add([
            'title'      => $title,
            'status'     => 'new',
            'address'    => $address,
            'sum'        => $sum,
            'courier_id' => 1, // по умолчанию Ашот
        ]);

        redirect('/orders');
    }
}