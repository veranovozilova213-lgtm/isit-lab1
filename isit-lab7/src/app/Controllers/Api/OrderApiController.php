<?php
declare(strict_types=1);
namespace App\Controllers\Api;

use App\Models\OrderRepository;
use App\Models\CourierRepository;

final class OrderApiController
{
    private OrderRepository $orders;
    private CourierRepository $couriers;

    public function __construct()
    {
        $this->orders = new OrderRepository();
        $this->couriers = new CourierRepository();
    }

    public function index(): void
    {
        $orders = $this->orders->all();
        $status = $_GET['status'] ?? '';
        if ($status !== '') {
            $orders = array_values(array_filter($orders, fn(array $t): bool => $t['status'] === $status));
        }
        json_response($orders);
    }

    public function show(string $id): void
    {
        $order = $this->orders->find((int) $id);
        if ($order === null) {
            json_error('Заказ не найден', 404);
        }
        json_response($order);
    }

    public function store(): void
    {
        $data = json_input();
        $errors = $this->validate($data);
        if ($errors) {
            json_error('Ошибка проверки данных', 422, $errors);
        }
        $order = $this->orders->add($this->clean($data));
        header('Location: /api/orders/' . $order['id']);
        json_response($order, 201);
    }

    public function update(string $id): void
    {
        $order = $this->orders->find((int) $id);
        if ($order === null) {
            json_error('Заказ не найден', 404);
        }
        $data = array_merge($order, json_input());
        $errors = $this->validate($data);
        if ($errors) {
            json_error('Ошибка проверки данных', 422, $errors);
        }
        json_response($this->orders->update($order['id'], $this->clean($data)));
    }

    public function destroy(string $id): void
    {
        if (!$this->orders->delete((int) $id)) {
            json_error('Заказ не найден', 404);
        }
        no_content();
    }

    private function clean(array $data): array
    {
        return [
            'courier_id' => (int) ($data['courier_id'] ?? 0),
            'title'      => trim($data['title'] ?? ''),
            'status'     => $data['status'] ?? 'new',
            'address'    => trim($data['address'] ?? ''),
            'sum'        => (int) ($data['sum'] ?? 0),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['title']) || mb_strlen(trim($data['title'])) < 3) {
            $errors['title'] = 'Название (номер) — не короче 3 символов';
        }
        if (empty($data['courier_id']) || $this->couriers->find((int)$data['courier_id']) === null) {
            $errors['courier_id'] = 'Выберите существующего курьера (courier_id)';
        }
        if (!in_array($data['status'] ?? null, ['new', 'in_progress', 'done'], true)) {
            $errors['status'] = 'Статус: new, in_progress или done';
        }
        return $errors;
    }
}